<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Facades\Config;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;

use function array_map;
use function array_values;
use function in_array;
use function json_encode;
use function preg_replace;
use function sprintf;

use const PHP_INT_MAX;

class DashboardWidgets implements Hookable
{
	/** @phpstan-var list<array{id:string,title:string}> */
	private static array $widgets = [];

	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_enqueue_scripts', [$this, 'addInlineScripts']);
		$hook->addAction('admin_init', static fn () => self::$widgets = self::getRegisteredWidgets());
		$hook->addAction('wp_dashboard_setup', [$this, 'setup'], PHP_INT_MAX);
		$hook->addFilter('syntatis/feature_flipper/settings', [$this, 'setSettings']);
	}

	public function setup(): void
	{
		if (! Option::get('dashboard_widgets')) {
			// phpcs:ignore
			$GLOBALS['wp_meta_boxes']['dashboard'] = [];

			return;
		}

		self::setupEach();
	}

	private static function setupEach(): void
	{
		if (self::$widgets === []) {
			return;
		}

		$dashboardWidgets = $GLOBALS['wp_meta_boxes']['dashboard'] ?? null;
		$values = Option::get('dashboard_widgets_enabled') ?? self::getAllDashboardId();

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
		$optionName = Config::get('app.option_prefix') . 'dashboard_widgets_enabled';
		$widgetsEnabled = $data[$optionName] ?? null;

		if ($widgetsEnabled === null) {
			$data[$optionName] = self::getAllDashboardId();
		}

		return $data;
	}

	public function addInlineScripts(): void
	{
		wp_add_inline_script(
			App::name()	. '-settings',
			self::getInlineScript(),
			'before',
		);
	}

	private static function getInlineScript(): string
	{
		return sprintf(
			<<<'SCRIPT'
			window.$syntatis.featureFlipper = Object.assign({}, window.$syntatis.featureFlipper, {
				dashboardWidgets: %s,
			});
			SCRIPT,
			json_encode(self::$widgets),
		);
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
			self::$widgets,
		));
	}

	/**
	 * Get the list of dashboard widgets.
	 *
	 * @return array<{id:string,title:string,value:bool}> List of dashboard widgets.
	 * @phpstan-return list<array{id:string,title:string,value:bool}>
	 */
	private static function getRegisteredWidgets(): array
	{
		if (self::$widgets !== []) {
			return self::$widgets;
		}

		if (($GLOBALS['wp_meta_boxes']['dashboard'] ?? null) === null) {
			require_once ABSPATH . '/wp-admin/includes/dashboard.php';

			set_current_screen('dashboard');
			wp_dashboard_setup();
		}

		$optionName = Config::get('app.option_prefix') . 'dashboard_widgets_hidden';
		$dashboardWidgets = $GLOBALS['wp_meta_boxes']['dashboard'] ?? null;
		$widgetsHidden = $settings[$optionName] ?? [];
		$widgets = [];

		if ($dashboardWidgets === null) {
			return $widgets;
		}

		foreach ($dashboardWidgets as $items) {
			foreach ($items as $context => $item) {
				foreach ($item as $widgetId => $widget) {
					$widgets[] = [
						'id' => $widgetId,
						'title' => isset($widget['title']) ?
							wp_strip_all_tags(preg_replace('/ <span.*span>/im', '', $widget['title'])) :
							'-- Unknown --',
						'value' => $widgetsHidden[$widgetId] ?? false,
					];
				}
			}
		}

		return array_values(wp_list_sort($widgets, 'title', 'ASC', true));
	}
}
