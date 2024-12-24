<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use stdClass;
use Syntatis\FeatureFlipper\Helpers\Option;

use function define;
use function defined;
use function is_object;
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
			$hook->addFilter('site_transient_update_core', [$this, 'filterSiteTransientUpdate']);
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
		$hook->addFilter('site_status_tests', [$this, 'filterSiteStatusTests']);
	}

	/**
	 * Prune the transient the Core update information.
	 *
	 * This will effectively also remove the Update notification in the admin
	 * area, in case the update information was already fetched before the
	 * Core update feature is disabled.
	 *
	 * @see https://github.com/WordPress/WordPress/blob/master/wp-admin/includes/update.php#L54
	 *
	 * @param mixed $cache The WordPress Core update information cache.
	 */
	public function filterSiteTransientUpdate($cache): object
	{
		// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- Core WordPress convention.

		$cache = is_object($cache) ? $cache : new stdClass();

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

	/**
	 * Prevent the Core update check from being scheduled.
	 *
	 * @return object|false
	 */
	public function filterScheduleEvent(object $event)
	{
		if (property_exists($event, 'hook') && $event->hook === 'wp_version_check') {
			return false;
		}

		return $event;
	}

	/**
	 * Remove the Core update from the "Site Health" tests and report.
	 *
	 * WordPress will check for the Core update status in and will report it as
	 * in the "Site Health" status. This filter will exclude these tests and
	 * will remove them from the report since the Core update is disabled
	 * intentionally.
	 *
	 * @param array<string,array<string,mixed>> $tests
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function filterSiteStatusTests(array $tests): array
	{
		unset($tests['async']['background_updates']);
		unset($tests['direct']['plugin_theme_auto_updates']);

		return $tests;
	}
}
