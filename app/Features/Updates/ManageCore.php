<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;

use function define;
use function defined;
use function property_exists;

/**
 * Manage the core update and auto-update feature.
 */
class ManageCore implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('updates_core')) {
			$hook->addAction('admin_init', static function () use ($hook): void {
				$hook->removeAction('admin_init', '_maybe_update_core');
				$hook->removeAction('admin_init', 'wp_auto_update_core');
				$hook->removeAction('admin_init', 'wp_maybe_auto_update');
				$hook->removeAction('wp_maybe_auto_update', 'wp_maybe_auto_update');
				$hook->removeAction('wp_version_check', 'wp_version_check');
			});
			$hook->addFilter('site_transient_update_core', [$this, 'filterCoreUpdateTransient']);
			$hook->addFilter('send_core_update_notification_email', static fn (): bool => false);
		}

		if ((bool) Option::get('auto_update_core')) {
			return;
		}

		if (! defined('WP_AUTO_UPDATE_CORE')) {
			define('WP_AUTO_UPDATE_CORE', false);
		}

		$hook->addFilter('allow_dev_auto_core_updates', static fn (): bool => false);
		$hook->addFilter('allow_major_auto_core_updates', static fn (): bool => false);
		$hook->addFilter('allow_minor_auto_core_updates', static fn (): bool => false);
		$hook->addFilter('auto_core_update_send_email', static fn (): bool => false);
		$hook->addFilter('auto_update_core', static fn (): bool => false);
	}

	/**
	 * Filter the transient to remove the core update information.
	 *
	 * This prevents the notification from being displayed in the admin area,
	 * in case the update information was already fetched before the update
	 * feature was disabled.
	 */
	public function filterCoreUpdateTransient(object $cache): object
	{
		if (property_exists($cache, 'updates')) {
			$cache->updates = [];
		}

		return $cache;
	}
}
