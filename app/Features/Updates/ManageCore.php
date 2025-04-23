<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Filter;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Updates\Helpers\AutoUpdate;
use Syntatis\FeatureFlipper\Features\Updates\Helpers\Updates;
use Syntatis\FeatureFlipper\Helpers\Option;

use function define;
use function defined;
use function property_exists;
use function time;

/**
 * Manage Core update and auto-update feature.
 */
final class ManageCore implements Hookable
{
	public function hook(Hook $hook): void
	{
		$updatesFn = static fn (mixed $value) => Updates::components((bool) $value)->isEnabled();
		$autoUpdateFn = static fn (mixed $value): bool => Option::isOn('update_core') ?
			AutoUpdate::components((bool) $value)->isEnabled() :
			false;

		$hook->parse($this);
		$hook->addFilter(Option::hook('default:auto_update_core'), $autoUpdateFn);
		$hook->addFilter(Option::hook('default:update_core'), $updatesFn);
		$hook->addFilter(Option::hook('auto_update_core'), $autoUpdateFn);
		$hook->addFilter(Option::hook('update_core'), $updatesFn);

		if (! Option::isOn('update_core')) {
			$hook->addFilter('send_core_update_notification_email', '__return_false');
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
	#[Filter(name: 'site_transient_update_core')]
	public function siteTransientUpdateCore(mixed $cache = null): mixed
	{
		if (Option::isOn('update_core')) {
			return $cache;
		}

		return (object) [
			'updates' => [],
			'translations' => [],
			'version_checked' => $GLOBALS['wp_version'] ?? '',
			'last_checked' => time(),
		];
	}

	/**
	 * Prevent the Core update check from being scheduled.
	 */
	#[Filter(name: 'schedule_event')]
	public function scheduleEvent(object $event): object|false
	{
		if (Option::isOn('update_core')) {
			return $event;
		}

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
	#[Filter(name: 'site_status_tests')]
	public function filterSiteStatusTests(array $tests): array
	{
		if (Option::isOn('update_core')) {
			return $tests;
		}

		unset($tests['async']['background_updates']);
		unset($tests['direct']['plugin_theme_auto_updates']);

		return $tests;
	}
}
