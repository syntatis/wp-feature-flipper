<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use stdClass;
use Syntatis\FeatureFlipper\Helpers\Option;

use function is_object;
use function property_exists;
use function time;

/**
 * Manage the themes update and auto-update feature.
 */
class ManageThemes implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('update_themes')) {
			$hook->removeAction('admin_init', '_maybe_update_themes');
			$hook->removeAction('load-themes.php', 'wp_theme_update_rows', 20);
			$hook->removeAction('load-themes.php', 'wp_update_themes');
			$hook->removeAction('load-update-core.php', 'wp_update_themes');
			$hook->removeAction('load-update.php', 'wp_update_themes');
			$hook->removeAction('wp_update_themes', 'wp_update_themes');

			$hook->addFilter('site_transient_update_themes', [$this, 'filterSiteTransientUpdate']);
		}

		if ((bool) Option::get('auto_update_themes')) {
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
	 * @param mixed $cache The Plugins update information cache.
	 */
	public function filterSiteTransientUpdate($cache): object
	{
		$cache = is_object($cache) ? $cache : new stdClass();

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
