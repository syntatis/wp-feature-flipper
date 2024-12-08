<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;

use function define;
use function defined;
use function property_exists;
use function time;

/**
 * Manage the core update and auto-update feature.
 */
class ManageCore implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('update_core')) {
			$hook->removeAction('admin_init', '_maybe_update_core');
			$hook->removeAction('wp_maybe_auto_update', 'wp_maybe_auto_update');
			$hook->removeAction('wp_version_check', 'wp_version_check');

			$hook->addFilter('schedule_event', [$this, 'filterScheduleEvent']);
			$hook->addFilter('send_core_update_notification_email', '__return_false');
			$hook->addFilter('site_transient_update_core', [$this, 'filterUpdateTransient']);
		}

		if ((bool) Option::get('auto_update_core')) {
			return;
		}

		if (! defined('WP_AUTO_UPDATE_CORE')) {
			define('WP_AUTO_UPDATE_CORE', false);
		}

		$hook->addFilter('allow_dev_auto_core_updates', '__return_false');
		$hook->addFilter('allow_major_auto_core_updates', '__return_false');
		$hook->addFilter('allow_minor_auto_core_updates', '__return_false');
		$hook->addFilter('auto_core_update_send_email', '__return_false');
		$hook->addFilter('auto_update_core', '__return_false');
		$hook->addFilter('automatic_updates_is_vcs_checkout', '__return_false', 1);
	}

	/**
	 * Filter the transient to remove the core update information.
	 *
	 * This prevents the notification from being displayed in the admin area,
	 * in case the update information was already fetched before the update
	 * feature was disabled.
	 */
	public function filterUpdateTransient(object $cache): object
	{
		// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- Core WordPress convention.

		if (property_exists($cache, 'updates')) {
			$cache->updates = [];
		}

		if (property_exists($cache, 'translations')) {
			$cache->translations = [];
		}

		if (property_exists($cache, 'last_checked')) {
			$cache->last_checked = time();
		}

		if (property_exists($cache, 'version_checked') && isset($GLOBALS['wp_version'])) {
			$cache->version_checked = $GLOBALS['wp_version'];
		}

		// phpcs:enable

		return $cache;
	}

	/** @return object|false */
	public function filterScheduleEvent(object $event)
	{
		if (property_exists($event, 'hook') && $event->hook === 'wp_version_check') {
			return false;
		}

		return $event;
	}
}
