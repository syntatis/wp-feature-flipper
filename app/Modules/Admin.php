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

use function version_compare;
use function wp_dequeue_style;

final class Admin implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
		if (
			version_compare(get_bloginfo('version'), '7.0') >= 0 &&
			! Option::isOn('admin_view_transitions')
		) {
			$hook->addAction('admin_enqueue_scripts', [$this, 'disableViewTransitions'], 11);
		}

		if (! is_admin() || Option::isOn('admin_footer_text')) {
			return;
		}

		$hook->addFilter('admin_footer_text', '__return_empty_string', 99);
		$hook->addFilter('update_footer', '__return_empty_string', 99);
	}

	/**
	 * Disable the admin View Transitions introduced in WordPress 7.0.
	 *
	 * @see https://core.trac.wordpress.org/ticket/64470
	 */
	public function disableViewTransitions(): void
	{
		wp_dequeue_style('wp-view-transitions-admin');
	}

	/** @inheritDoc */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield 'admin_bar' => new AdminBar();
		yield 'dashboard_widgets' => is_admin() ? new DashboardWidgets() : null;
	}
}
