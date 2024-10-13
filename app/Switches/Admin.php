<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Switches;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;

class Admin implements Hookable
{
	public function hook(Hook $hook): void
	{
		// 1. Admin WordPress Logo.
		if (! Option::get('admin_wordpress_logo')) {
			$hook->addAction('admin_bar_menu', static fn ($wpAdminBar) => $wpAdminBar->remove_node('wp-logo'), 99);
		}

		// 2. Admin Footer Text.
		if (! Option::get('admin_footer_text')) {
			$hook->addFilter('admin_footer_text', '__return_empty_string', 99);
			$hook->addFilter('update_footer', '__return_empty_string', 99);
		}

		// 3. Update Nags.
		if (Option::get('update_nags')) {
			return;
		}

		$hook->addAction('admin_init', static function (): void {
			remove_action('admin_notices', 'update_nag', 3);
			remove_action('network_admin_notices', 'update_nag', 3);
		}, 99);
	}
}
