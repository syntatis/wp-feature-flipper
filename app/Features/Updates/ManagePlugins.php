<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;

use function property_exists;
use function time;

/**
 * Manage the plugins update and auto-update feature.
 */
class ManagePlugins implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('update_plugins')) {
			$hook->removeAction('admin_init', '_maybe_update_plugins');
			$hook->removeAction('load-plugins.php', 'wp_plugin_update_rows', 20);
			$hook->removeAction('load-plugins.php', 'wp_update_plugins');
			$hook->removeAction('load-update-core.php', 'wp_update_plugins');
			$hook->removeAction('load-update.php', 'wp_update_plugins');

			$hook->addFilter('site_transient_update_plugins', [$this, 'filterUpdateTransient']);
		}

		if ((bool) Option::get('auto_update_plugins')) {
			return;
		}

		$hook->addFilter('auto_update_plugin', '__return_false');
	}

	public function filterUpdateTransient(object $cache): object
	{
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
