<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use SSFV\Codex\Facades\App;
use Syntatis\FeatureFlipper\Concerns\DontInstantiate;
use WP_Screen;

use function array_merge;
use function function_exists;
use function is_string;

/**
 * A collection of methods to interact with the WordPress admin area.
 */
final class Admin
{
	use DontInstantiate;

	/**
	 * Get the URL in the admin for the provided address.
	 *
	 * @param string              $address The path, or the name of the page.
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

	/** @phpstan-param non-empty-string $address */
	public static function isScreen(string $address): bool
	{
		if (! is_admin()) {
			return false;
		}

		switch ($address) {
			case App::name():
				return self::isPluginSettingPage();

			default:
				if (Str::endsWith($address, '.php')) {
					$pagenow = $GLOBALS['pagenow'] ?? '';

					if (is_string($pagenow) && $pagenow === $address) {
						return true;
					}
				}

				if (! function_exists('get_current_screen')) {
					return false;
				}

				$screen = get_current_screen();

				if ($screen instanceof WP_Screen) {
					return $screen->id === $address;
				}

				return false;
		}
	}

	/**
	 * Whether the current view is the plugin setting page.
	 */
	private static function isPluginSettingPage(): bool
	{
		$screenId = 'settings_page_' . App::name();
		$pageNow = $GLOBALS['pagenow'] ?? null;
		$pageHook = $GLOBALS['page_hook'] ?? null;

		if ($pageNow === 'options-general.php' && $pageHook === $screenId) {
			return true;
		}

		if (! function_exists('get_current_screen')) {
			return false;
		}

		$screen = get_current_screen();

		return $screen instanceof WP_Screen ?
			$screen->id === $screenId :
			false;
	}
}
