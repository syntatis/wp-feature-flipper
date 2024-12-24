<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\Updates;

use function is_object;
use function property_exists;
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
		$autoUpdateFn = static fn ($value) => Updates::plugins()->isEnabled((bool) $value);

		$hook->addFilter(self::defaultOptionName('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionName('update_plugins'), $updatesFn);
		$hook->addFilter(self::optionName('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(self::optionName('update_plugins'), $updatesFn);

		if (! (bool) Option::get('update_plugins')) {
			$hook->removeAction('admin_init', '_maybe_update_plugins');
			$hook->removeAction('load-plugins.php', 'wp_plugin_update_rows', 20);
			$hook->removeAction('load-plugins.php', 'wp_update_plugins');
			$hook->removeAction('load-update-core.php', 'wp_update_plugins');
			$hook->removeAction('load-update.php', 'wp_update_plugins');
			$hook->removeAction('wp_update_plugins', 'wp_update_plugins');
			$hook->addFilter('site_transient_update_plugins', [$this, 'filterSiteTransientUpdate']);
		}

		if ((bool) Option::get('auto_update_plugins')) {
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
	 *
	 * @return object|bool
	 */
	public function filterSiteTransientUpdate($cache)
	{
		if (! is_object($cache)) {
			return $cache;
		}

		if (property_exists($cache, 'response')) {
			$cache->response = [];
		}

		if (property_exists($cache, 'translations')) {
			$cache->translations = [];
		}

		if (property_exists($cache, 'last_checked')) {
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- Core WordPress convention.
			$cache->last_checked = time();
		}

		return $cache;
	}
}
