<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\Heartbeat\Heartbeat;
use Syntatis\FeatureFlipper\Features\Updates\Updates;
use Syntatis\FeatureFlipper\Helpers\Option;

use function define;
use function defined;

use const PHP_INT_MAX;

final class Advanced implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
		if (! Option::isOn('update_nags')) {
			$hook->addAction('admin_init', static function () use ($hook): void {
				$hook->removeAction('admin_notices', 'update_nag', 3);
				$hook->removeAction('network_admin_notices', 'update_nag', 3);
			}, 99);
		}

		$hook->addFilter(
			'pre_wp_mail',
			static function ($value) {
				return (bool) Option::get('mail_sending') ? $value : false;
			},
			PHP_INT_MAX,
		);

		if (Option::isOn('cron') || defined('DISABLE_WP_CRON')) {
			return;
		}

		define('DISABLE_WP_CRON', true);
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new Heartbeat();
		yield new Updates();
	}
}
