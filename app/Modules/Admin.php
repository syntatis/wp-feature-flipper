<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SFFV\Codex\Contracts\Extendable;
use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use SFFV\Psr\Container\ContainerInterface;
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

	/** @inheritDoc */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield 'admin_bar' => new AdminBar();
		yield 'dashboard_widgets' => is_admin() ? new DashboardWidgets() : null;
	}
}
