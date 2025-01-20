<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\InlineData;
use WP_Screen;

use function array_map;
use function array_merge;
use function array_values;
use function function_exists;
use function in_array;
use function is_array;
use function preg_replace;

use const PHP_INT_MAX;

/**
 * @phpstan-type DashboardWidget array{id:string,title:string,callback:string,args:array<string,mixed>}|bool
 * @phpstan-type DashboardWidgetCollection array<"normal"|"side",array<string,array<string,DashboardWidget>>>
 */
class DashboardWidgets implements Hookable
{
	private static bool $onSettingPage = false;

	/** @var array<string,array{id:string,title:string}> */
	private static ?array $widgets = null;

	public function hook(Hook $hook): void
	{
		$hook->addFilter('syntatis/feature_flipper/inline_data', [$this, 'filterInlineData']);
		$hook->addFilter(
			Option::hook('default:dashboard_widgets_enabled'),
			static fn (): array => self::getAllDashboardId(),
			PHP_INT_MAX,
		);
		$hook->addAction('current_screen', static function (WP_Screen $screen): void {
			if ($screen->id !== 'settings_page_' . App::name()) {
				return;
			}

			if (is_array(self::$widgets)) {
				return;
			}

			self::$onSettingPage = true;
			self::$widgets = self::getRegisteredWidgets($screen);
			self::$onSettingPage = false;
		});
		$hook->addAction('wp_dashboard_setup', [$this, 'setup'], PHP_INT_MAX);
	}

	public function setup(): void
	{
		if (self::$onSettingPage) {
			return;
		}

		if (! Option::isOn('dashboard_widgets')) {
			// @phpstan-ignore offsetAccess.nonOffsetAccessible
			$GLOBALS['wp_meta_boxes']['dashboard'] = []; // phpcs:ignore

			return;
		}

		self::setupEach();
	}

	private static function setupEach(): void
	{
		$dashboardWidgets = self::getRawWidgets() ?? [];
		$values = Option::get('dashboard_widgets_enabled');
		$values = is_array($values) ? $values : [];

		foreach ($dashboardWidgets as $a => $items) {
			foreach ($items as $b => $item) {
				foreach ($item as $widgetId => $widget) {
					if (in_array($widgetId, $values, true)) {
						continue;
					}

					unset($dashboardWidgets[$a][$b][$widgetId]);
				}
			}
		}

		// @phpstan-ignore offsetAccess.nonOffsetAccessible
		$GLOBALS['wp_meta_boxes']['dashboard'] = $dashboardWidgets; // phpcs:ignore
	}

	public function filterInlineData(InlineData $data): InlineData
	{
		$tab = $_GET['tab'] ?? null;

		if ($tab !== 'admin') {
			return $data;
		}

		$curr = $data['$wp'] ?? [];
		$data['$wp'] = array_merge(
			is_array($curr) ? $curr : [],
			[
				'dashboardWidgets' => self::$widgets,
			],
		);

		return $data;
	}

	/**
	 * Get the list of dashboard widgets.
	 *
	 * @return array<string> List of dashboard widgets.
	 * @phpstan-return list<string>
	 */
	private static function getAllDashboardId(): array
	{
		if (! function_exists('get_current_screen')) {
			return [];
		}

		$screen = get_current_screen();

		if (! $screen instanceof WP_Screen) {
			return [];
		}

		return array_values(
			array_map(
				static fn (array $widget): string => $widget['id'],
				self::getRegisteredWidgets($screen) ?? [],
			),
		);
	}

	/**
	 * Get the list of dashboard widgets.
	 *
	 * @return array<string,array{id:string,title:string}>|null List of dashboard widgets.
	 */
	private static function getRegisteredWidgets(WP_Screen $screen): ?array
	{
		/** @var array<string,array{id:string,title:string}>|null $widgets */
		static $widgets = null;

		if (is_array($widgets)) {
			return $widgets;
		}

		if (self::getRawWidgets() === null && $screen->id !== 'dashboard') {
			// @phpstan-ignore requireOnce.fileNotFound
			require_once ABSPATH . '/wp-admin/includes/dashboard.php';

			set_current_screen('dashboard');
			wp_dashboard_setup();

			$dashboardWidgets = self::getRawWidgets();

			// @phpstan-ignore offsetAccess.nonOffsetAccessible
			unset($GLOBALS['wp_meta_boxes']['dashboard']);
		} else {
			$dashboardWidgets = self::getRawWidgets();
		}

		foreach ((array) $dashboardWidgets as $items) {
			foreach ($items as $context => $item) {
				foreach ($item as $widgetId => $widget) {
					if (! is_array($widget)) {
						continue;
					}

					$widgets[$widgetId] = [
						'id' => $widgetId,
						'title' => $widget['title'] !== ''
							? wp_strip_all_tags((string) preg_replace('/ <span.*span>/im', '', $widget['title']))
							: '-- ' . __('Unknown', 'syntatis-feature-flipper') . ' --',
					];
				}
			}
		}

		set_current_screen($screen->id);

		return is_array($widgets) ? wp_list_sort($widgets, 'title', 'ASC', true) : null;
	}

	/**
	 * @return array<string,array<string,array<string,array<string,mixed>>>>
	 * @phpstan-return DashboardWidgetCollection
	 */
	private static function getRawWidgets(): ?array
	{
		if (! isset($GLOBALS['wp_meta_boxes']) || ! is_array($GLOBALS['wp_meta_boxes'])) {
			return null;
		}

		if (isset($GLOBALS['wp_meta_boxes']['dashboard']) && is_array($GLOBALS['wp_meta_boxes']['dashboard'])) {
			/** @phpstan-var DashboardWidgetCollection $metaboxes */
			$metaboxes = $GLOBALS['wp_meta_boxes']['dashboard'];

			return $metaboxes;
		}

		return null;
	}
}
