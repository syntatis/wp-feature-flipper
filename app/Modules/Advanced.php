<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\Heartbeat;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Helpers\Option;

use function define;
use function defined;

class Advanced implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
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
