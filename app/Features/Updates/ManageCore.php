<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Helpers\AutoUpdate;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\Updates;

use function define;
use function defined;
use function property_exists;
use function time;

/**
 * Manage Core update and auto-update feature.
 */
class ManageCore implements Hookable
{
	use WithHookName;

	public function hook(Hook $hook): void
	{
		$updatesFn = static fn ($value) => Updates::core()->isEnabled((bool) $value);
		$autoUpdateFn = static function ($value): bool {
			if (! Option::isOn('update_core')) {
				return false;
			}

			return AutoUpdate::core()->isEnabled((bool) $value);
		};

		$hook->addFilter(self::defaultOptionHook('auto_update_core'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionHook('update_core'), $updatesFn);
		$hook->addFilter(self::optionHook('auto_update_core'), $autoUpdateFn);
		$hook->addFilter(self::optionHook('update_core'), $updatesFn);

		if (! Option::isOn('update_core')) {
			$hook->addFilter('schedule_event', [$this, 'filterScheduleEvent']);
			$hook->addFilter('send_core_update_notification_email', '__return_false');
			$hook->addFilter('site_transient_update_core', [$this, 'filterSiteTransientUpdate']);
			$hook->addFilter('site_status_tests', [$this, 'filterSiteStatusTests']);
			$hook->removeAction('admin_init', '_maybe_update_core');
			$hook->removeAction('wp_maybe_auto_update', 'wp_maybe_auto_update');
			$hook->removeAction('wp_version_check', 'wp_version_check');
		}

		if (Option::isOn('auto_update_core')) {
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
	public function filterSiteTransientUpdate($cache = null): object
	{
		return (object) [
			'updates' => [],
			'translations' => [],
			'version_checked' => $GLOBALS['wp_version'] ?? '',
			'last_checked' => time(),
		];
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
