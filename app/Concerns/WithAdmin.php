<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use SSFV\Codex\Facades\App;
use WP_Screen;

use function array_merge;
use function function_exists;

/**
 * A collection of methods to interact with the WordPress admin area.
 */
trait WithAdmin
{
	/**
	 * Retrieve the URL to the plugin setting page.
	 *
	 * @param array<string,mixed> $args Additional query arguments.
	 */
	private static function getSettingPageURL(array $args = []): string
	{
		return add_query_arg(
			array_merge(
				$args,
				[
					'page' => App::name(),
				],
			),
			admin_url('options-general.php'),
		);
	}

	/**
	 * Whether the current view is the plugin setting page.
	 */
	private static function isSettingPage(): bool
	{
		if (! is_admin() || ! function_exists('get_current_screen')) {
			return false;
		}

		$currentScreen = get_current_screen();

		if ($currentScreen === null) {
			return false;
		}

		return $currentScreen->id === 'settings_page_' . App::name();
	}

	/**
	 * Whether the current view is the admin "Dashboard".
	 */
	private static function isDashboardPage(): bool
	{
		if (! is_admin()) {
			return false;
		}

		$currentScreen = null;

		if (function_exists('get_current_screen')) {
			$currentScreen = get_current_screen();
		}

		if ($currentScreen instanceof WP_Screen) {
			return $currentScreen->id === 'dashboard';
		}

		return ($GLOBALS['pagenow'] ?? '') === 'index.php';
	}
}
