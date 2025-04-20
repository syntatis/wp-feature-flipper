<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Updates\Helpers\AutoUpdate;
use Syntatis\FeatureFlipper\Features\Updates\Helpers\Updates;
use Syntatis\FeatureFlipper\Helpers\Option;

use function time;

/**
 * Manage the Plugins update and auto-update feature.
 */
final class ManagePlugins implements Hookable
{
	public function hook(Hook $hook): void
	{
		$updatesFn = static fn ($value) => Updates::components((bool) $value)->isEnabled();
		$autoUpdateFn = static fn ($value): bool => Option::isOn('update_plugins') ?
			AutoUpdate::components((bool) $value)->isEnabled() :
			false;

		$hook->addFilter(Option::hook('default:auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(Option::hook('default:update_plugins'), $updatesFn);
		$hook->addFilter(Option::hook('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(Option::hook('update_plugins'), $updatesFn);

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
	 */
	public function filterSiteTransientUpdate(object|bool $cache): object
	{
		return (object) [
			'response' => [],
			'translations' => [],
			'last_checked' => time(),
		];
	}
}
