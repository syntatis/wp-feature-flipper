<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Screen;

use function array_map;
use function array_values;
use function count;
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

	/** @var array<array{id:string,title:string,value:bool}> */
	private static array $widgets = [];

	public function hook(Hook $hook): void
	{
		$hook->addFilter('syntatis/feature_flipper/settings', [$this, 'addSettingsData']);
		$hook->addFilter('syntatis/inline_data', [$this, 'addInlineData']);
		$hook->addAction('current_screen', static function (WP_Screen $screen): void {
			if ($screen->id !== 'settings_page_' . App::name()) {
				return;
			}

			self::$onSettingPage = true;
			self::$widgets = self::getRegisteredWidgets();
			self::$onSettingPage = false;
		});
		$hook->addAction('wp_dashboard_setup', [$this, 'setup'], PHP_INT_MAX);
	}

	public function setup(): void
	{
		if (self::$onSettingPage) {
			return;
		}

		if (! (bool) Option::get('dashboard_widgets')) {
			// @phpstan-ignore offsetAccess.nonOffsetAccessible
			$GLOBALS['wp_meta_boxes']['dashboard'] = []; // phpcs:ignore

			return;
		}

		self::setupEach();
	}

	private static function setupEach(): void
	{
		$dashboardWidgets = self::getRawWidgets() ?? [];
		$values = Option::get('dashboard_widgets_enabled') ?? self::getAllDashboardId();
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
	 * @param array<mixed> $data
	 *
	 * @return array<mixed>
	 */
	public function addSettingsData(array $data): array
	{
		$optionName = Option::name('dashboard_widgets_enabled');
		$widgetsEnabled = $data[$optionName] ?? null;

		if ($widgetsEnabled === null) {
			$data[$optionName] = self::getAllDashboardId();
		}

		return $data;
	}

	/**
	 * @param array<string,mixed> $data
	 *
	 * @return array<mixed>
	 */
	public function addInlineData(array $data): array
	{
		$data['dashboardWidgets'] = self::$widgets;

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
		return array_values(
			array_map(
				static fn (array $widget): string => $widget['id'],
				self::getRegisteredWidgets(),
			),
		);
	}

	/**
	 * Get the list of dashboard widgets.
	 *
	 * @return array<array{id:string,title:string,value:bool}> List of dashboard widgets.
	 */
	private static function getRegisteredWidgets(): array
	{
		/** @var array<array{id:string,title:string,value:bool}>|null $widgets */
		static $widgets = null;

		if (is_array($widgets) && count($widgets) > 0) {
			return $widgets;
		}

		$currentScreen = get_current_screen();

		if (
			self::getRawWidgets() === null &&
			$currentScreen instanceof WP_Screen &&
			$currentScreen->id !== 'dashboard'
		) {
			// @phpstan-ignore requireOnce.fileNotFound
			require_once ABSPATH . '/wp-admin/includes/dashboard.php';

			set_current_screen('dashboard');
			wp_dashboard_setup();
			set_current_screen($currentScreen->id);

			$dashboardWidgets = self::getRawWidgets();

			// @phpstan-ignore offsetAccess.nonOffsetAccessible
			unset($GLOBALS['wp_meta_boxes']['dashboard']);
		} else {
			$dashboardWidgets = self::getRawWidgets();
		}

		$optionName = Option::name('dashboard_widgets_enabled');
		$widgets = [];

		if ($dashboardWidgets === null) {
			return $widgets;
		}

		$settings = Option::get('dashboard_widgets_enabled') ?? [];
		$settings = is_array($settings) ? $settings : [];
		$widgetsHidden = isset($settings[$optionName]) && is_array($settings[$optionName]) ?
			$settings[$optionName] :
			[];

		foreach ($dashboardWidgets as $items) {
			foreach ($items as $context => $item) {
				foreach ($item as $widgetId => $widget) {
					if (! is_array($widget)) {
						continue;
					}

					$widgets[] = [
						'id' => $widgetId,
						'title' => $widget['title'] !== ''
							? wp_strip_all_tags((string) preg_replace('/ <span.*span>/im', '', $widget['title']))
							: '-- ' . __('Unknown', 'syntatis-feature-flipper') . ' --',
						'value' => in_array($widgetId, $widgetsHidden, true),
					];
				}
			}
		}

		return array_values(wp_list_sort($widgets, 'title', 'ASC', true));
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
