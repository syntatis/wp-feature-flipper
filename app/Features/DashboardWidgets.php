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

class DashboardWidgets implements Hookable
{
	private static bool $onSettingPage = false;

	/** @phpstan-var list<array{id:string,title:string}> */
	private static array $widgets = [];

	public function hook(Hook $hook): void
	{
		$hook->addFilter('syntatis/feature_flipper/settings', [$this, 'setSettings']);
		$hook->addFilter('syntatis/feature_flipper/inline_data', [$this, 'setInlineData']);
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
			// phpcs:ignore
			$GLOBALS['wp_meta_boxes']['dashboard'] = [];

			return;
		}

		self::setupEach();
	}

	private static function setupEach(): void
	{
		$dashboardWidgets = $GLOBALS['wp_meta_boxes']['dashboard'] ?? null;
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

		// phpcs:ignore
		$GLOBALS['wp_meta_boxes']['dashboard'] = $dashboardWidgets;
	}

	/**
	 * @param array<mixed> $data
	 *
	 * @return array<mixed>
	 */
	public function setSettings(array $data): array
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
	public function setInlineData(array $data): array
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
		return array_values(array_map(
			static fn (array $widget): string => $widget['id'],
			self::getRegisteredWidgets(),
		));
	}

	/**
	 * Get the list of dashboard widgets.
	 *
	 * @return array<array{id:string,title:string,value:bool}> List of dashboard widgets.
	 * @phpstan-return list<array{id:string,title:string,value:bool}>
	 */
	private static function getRegisteredWidgets(): array
	{
		static $widgets = null;

		if (is_array($widgets) && count($widgets) > 0) {
			return $widgets;
		}

		$currentScreen = get_current_screen();

		if (
			($GLOBALS['wp_meta_boxes']['dashboard'] ?? null) === null &&
			$currentScreen instanceof WP_Screen &&
			$currentScreen->id !== 'dashboard'
		) {
			require_once ABSPATH . '/wp-admin/includes/dashboard.php';

			set_current_screen('dashboard');
			wp_dashboard_setup();
			set_current_screen($currentScreen->id);

			$dashboardWidgets = $GLOBALS['wp_meta_boxes']['dashboard'] ?? null;
			unset($GLOBALS['wp_meta_boxes']['dashboard']);
		} else {
			$dashboardWidgets = $GLOBALS['wp_meta_boxes']['dashboard'] ?? null;
		}

		$optionName = Option::name('dashboard_widgets_enabled');
		$widgets = [];

		if ($dashboardWidgets === null) {
			return $widgets;
		}

		$settings = Option::get('dashboard_widgets_enabled') ?? [];
		$settings = is_array($settings) ? $settings : [];
		$widgetsHidden = $settings[$optionName] ?? [];

		foreach ($dashboardWidgets as $items) {
			foreach ($items as $context => $item) {
				foreach ($item as $widgetId => $widget) {
					$widgets[] = [
						'id' => $widgetId,
						'title' => isset($widget['title']) ?
							wp_strip_all_tags(preg_replace('/ <span.*span>/im', '', $widget['title'])) :
							'-- Unknown --',
						'value' => in_array($widgetId, $widgetsHidden, true),
					];
				}
			}
		}

		return array_values(wp_list_sort($widgets, 'title', 'ASC', true));
	}
}
