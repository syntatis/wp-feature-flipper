<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\AdminBar\AdminBar;
use Syntatis\FeatureFlipper\Features\DashboardWidgets;
use Syntatis\FeatureFlipper\Helpers\Option;

final class Admin implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
		if (! is_admin() || Option::isOn('admin_footer_text')) {
			return;
		}

		$hook->addFilter('admin_footer_text', '__return_empty_string', 99);
		$hook->addFilter('update_footer', '__return_empty_string', 99);
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new AdminBar();
		yield is_admin() ? new DashboardWidgets() : null;
	}
}
