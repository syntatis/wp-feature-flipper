<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;

use function property_exists;

/**
 * Manage the themes update and auto-update feature.
 */
class ManageThemes implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('update_themes')) {
			$hook->addAction('admin_init', static function () use ($hook): void {
				$hook->removeAction('admin_init', '_maybe_update_themes');
				$hook->removeAction('load-themes.php', 'wp_theme_update_rows', 20);
				$hook->removeAction('load-themes.php', 'wp_update_themes');
				$hook->removeAction('load-update-core.php', 'wp_update_themes');
				$hook->removeAction('load-update.php', 'wp_update_themes');
				$hook->removeAction('wp_update_themes', 'wp_update_themes');
			});
			$hook->addFilter('site_transient_update_plugins', [$this, 'filterUpdateTransient']);
		}

		if ((bool) Option::get('auto_update_plugins')) {
			return;
		}

		$hook->addFilter('auto_update_plugin', static fn (): bool => false);
	}

	public function filterUpdateTransient(object $cache): object
	{
		if (property_exists($cache, 'response')) {
			$cache->response = [];
		}

		if (property_exists($cache, 'translations')) {
			$cache->translations = [];
		}

		return $cache;
	}
}
