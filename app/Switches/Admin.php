<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Switches;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;
use WP_Admin_Bar;

use const PHP_INT_MAX;

class Admin implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! Option::get('admin_footer_text')) {
			$hook->addFilter('admin_footer_text', '__return_empty_string', 99);
			$hook->addFilter('update_footer', '__return_empty_string', 99);
		}

		if (! Option::get('update_nags')) {
			$hook->addAction('admin_init', static function () use ($hook): void {
				$hook->removeAction('admin_notices', 'update_nag', 3);
				$hook->removeAction('network_admin_notices', 'update_nag', 3);
			}, 99);
		}


		$adminBar = new AdminBar();
		$adminBar->hook($hook);
	}
}
