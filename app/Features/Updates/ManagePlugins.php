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
 * Manage the Plugins update and auto-update feature.
 */
class ManagePlugins implements Hookable
{
	use WithHookName;

	public function hook(Hook $hook): void
	{
		$updatesFn = static fn ($value) => Updates::plugins()->isEnabled((bool) $value);
		$autoUpdateFn = static function ($value): bool {
			if (! Option::isOn('update_plugins')) {
				return false;
			}

			return AutoUpdate::plugins()->isEnabled((bool) $value);
		};

		$hook->addFilter(self::defaultOptionHook('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionHook('update_plugins'), $updatesFn);
		$hook->addFilter(self::optionHook('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(self::optionHook('update_plugins'), $updatesFn);

		if (! Option::isOn('update_plugins')) {
			$hook->removeAction('admin_init', '_maybe_update_plugins');
			$hook->removeAction('load-plugins.php', 'wp_plugin_update_rows', 20);
			$hook->removeAction('load-plugins.php', 'wp_update_plugins');
			$hook->removeAction('load-update-core.php', 'wp_update_plugins');
			$hook->removeAction('load-update.php', 'wp_update_plugins');
			$hook->removeAction('wp_update_plugins', 'wp_update_plugins');
			$hook->addFilter('site_transient_update_plugins', [$this, 'filterSiteTransientUpdate']);
		}

		if (Option::isOn('auto_update_plugins')) {
			return;
		}

		$hook->addFilter('auto_update_plugin', '__return_false');
	}

	/**
	 * Prune the Themes update information cache fetched from WordPress.org
	 *
	 * This will effectively also remove the Update notification in the admin
	 * area, in case the update information was already fetched before the
	 * Plugins update feature is disabled.
	 *
	 * @see https://github.com/WordPress/WordPress/blob/master/wp-admin/includes/update.php#L409
	 *
	 * @param object|bool $cache
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
