<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use SSFV\Codex\Facades\App;

use function array_merge;
use function function_exists;

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
}
