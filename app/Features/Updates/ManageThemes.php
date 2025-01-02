<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Helpers\AutoUpdate;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\Updates;

use function time;

/**
 * Manage the Themes update and auto-update feature.
 */
class ManageThemes implements Hookable
{
	use WithHookName;

	public function hook(Hook $hook): void
	{
		$updatesFn = static fn ($value) => Updates::themes()->isEnabled((bool) $value);
		$autoUpdateFn = static function ($value): bool {
			if (! Option::isOn('update_themes')) {
				return false;
			}

			return AutoUpdate::themes()->isEnabled((bool) $value);
		};

		$hook->addFilter(self::defaultOptionHook('auto_update_themes'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionHook('update_themes'), $updatesFn);
		$hook->addFilter(self::optionHook('auto_update_themes'), $autoUpdateFn);
		$hook->addFilter(self::optionHook('update_themes'), $updatesFn);

		if (! Option::isOn('update_themes')) {
			$hook->addFilter('site_transient_update_themes', [$this, 'filterSiteTransientUpdate']);
			$hook->removeAction('admin_init', '_maybe_update_themes');
			$hook->removeAction('load-themes.php', 'wp_theme_update_rows', 20);
			$hook->removeAction('load-themes.php', 'wp_update_themes');
			$hook->removeAction('load-update-core.php', 'wp_update_themes');
			$hook->removeAction('load-update.php', 'wp_update_themes');
			$hook->removeAction('wp_update_themes', 'wp_update_themes');
		}

		if (Option::isOn('auto_update_themes')) {
			return;
		}

		$hook->addFilter('auto_update_theme', '__return_false');
	}

	/**
	 * Prune the Plugins update information cache fetched from WordPress.org
	 *
	 * This will effectively also remove the Update notification in the admin
	 * area, in case the update information was already fetched before the
	 * Themes update feature is disabled.
	 *
	 * @param object|bool $cache The Plugins update information cache.
	 */
	public function filterSiteTransientUpdate($cache): object
	{
		return (object) [
			'response' => [],
			'translations' => [],
			'last_checked' => time(),
		];
	}
}
