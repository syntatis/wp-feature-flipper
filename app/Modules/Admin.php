<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\AdminBar;
use Syntatis\FeatureFlipper\Features\DashboardWidgets;
use Syntatis\FeatureFlipper\Helpers\Option;

class Admin implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('admin_footer_text')) {
			$hook->addFilter('admin_footer_text', '__return_empty_string', 99);
			$hook->addFilter('update_footer', '__return_empty_string', 99);
		}

		if ((bool) Option::get('update_nags')) {
			return;
		}

		$hook->addAction('admin_init', static function () use ($hook): void {
			$hook->removeAction('admin_notices', 'update_nag', 3);
			$hook->removeAction('network_admin_notices', 'update_nag', 3);
		}, 99);
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new DashboardWidgets();
		yield new AdminBar();
	}
}
