<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;

use function define;
use function defined;
use function property_exists;

class ManageCore implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('updates_core')) {
			$hook->addAction('schedule_event', [$this, 'filterCronEvents']);
			$hook->addFilter('send_core_update_notification_email', static fn (): bool => false);
			$hook->addAction('admin_init', static function () use ($hook): void {
				$hook->removeAction('admin_init', '_maybe_update_core');
				$hook->removeAction('admin_init', 'wp_auto_update_core');
				$hook->removeAction('admin_init', 'wp_maybe_auto_update');
				$hook->removeAction('wp_maybe_auto_update', 'wp_maybe_auto_update');
				$hook->removeAction('wp_version_check', 'wp_version_check');
			});
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

	/** @return object|false */
	public function filterCronEvents(object $event)
	{
		if (property_exists($event, 'hook') && $event->hook === 'wp_version_check') {
			return false;
		}

		return $event;
	}
}
