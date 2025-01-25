<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use ArrayAccess;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Admin;
use Syntatis\FeatureFlipper\Helpers\Option;
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
	public function hook(Hook $hook): void
	{
		$hook->addAction('syntatis/feature_flipper/updated_options', [$this, 'stashOptions']);
		$hook->addFilter('syntatis/feature_flipper/inline_data', [$this, 'filterInlineData']);
		$hook->addFilter(
			Option::hook('default:dashboard_widgets_enabled'),
			static fn (): array => self::getAllDashboardId(),
			PHP_INT_MAX,
		);
		$hook->addFilter(
			Option::hook('dashboard_widgets_enabled'),
			static fn ($value) => Option::patch(
				'dashboard_widgets_enabled',
				is_array($value) ? $value : [],
				self::getAllDashboardId(),
			),
			PHP_INT_MAX,
		);
		$hook->addAction('wp_dashboard_setup', [$this, 'setup'], PHP_INT_MAX);
	}

	public function setup(): void
	{
		if (Admin::isScreen(App::name())) {
			return;
		}

		if (! Option::isOn('dashboard_widgets')) {
			// @phpstan-ignore offsetAccess.nonOffsetAccessible
			$GLOBALS['wp_meta_boxes']['dashboard'] = []; // phpcs:ignore

			return;
		}

		self::setupEach();
	}

	/** @param array<string> $options List of option names that have been updated. */
	public function stashOptions(array $options): void
	{
		if (
			! in_array(
				Option::name('dashboard_widgets_enabled'),
				$options,
				true,
			)
		) {
			return;
		}

		Option::stash('dashboard_widgets_enabled', self::getAllDashboardId());
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

	/**
	 * @phpstan-param ArrayAccess<string,mixed> $data
	 *
	 * @phpstan-return ArrayAccess<string,mixed>
	 */
	public function filterInlineData(ArrayAccess $data): ArrayAccess
	{
		if (! Admin::isScreen(App::name())) {
			return $data;
		}

		$screen = get_current_screen();

		if (! $screen instanceof WP_Screen) {
			return $data;
		}

		$tab = $_GET['tab'] ?? null;

		if ($tab !== 'admin') {
			return $data;
		}

		$curr = $data['$wp'] ?? [];
		$data['$wp'] = array_merge(
			is_array($curr) ? $curr : [],
			[
				'dashboardWidgets' => self::getRegisteredWidgets($screen),
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
