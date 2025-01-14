<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use SSFV\Codex\Facades\App;
use WP_Screen;

use function array_merge;
use function function_exists;

/**
 * A collection of methods to interact with the WordPress admin area.
 */
final class Admin
{
	private function __construct()
	{
	}

	/**
	 * Get the URL in the admin for the provided address.
	 *
	 * @param string              $address The address to get the URL for.
	 * @param array<string,mixed> $args    Additional query arguments to append to the URL.
	 * @phpstan-param non-empty-string $address
	 */
	public static function url(string $address, array $args = []): string
	{
		switch ($address) {
			case App::name():
				return add_query_arg(
					array_merge(
						$args,
						['page' => $address],
					),
					admin_url('options-general.php'),
				);

			default:
				return add_query_arg($args, admin_url($address));
		}
	}

	/** @phpstan-param non-empty-string $id */
	public static function isScreen(string $id): bool
	{
		if (! is_admin()) {
			return false;
		}

		switch ($id) {
			case App::name():
				return self::isPluginSettingPage();

			case 'dashboard':
				return self::isDashboardScreen();

			default:
				if (! function_exists('get_current_screen')) {
					return false;
				}

				$screen = get_current_screen();

				if ($screen instanceof WP_Screen) {
					return $screen->id === $id;
				}

				return false;
		}
	}

	/**
	 * Whether the current view is the plugin setting page.
	 */
	private static function isPluginSettingPage(): bool
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
	private static function isDashboardScreen(): bool
	{
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
