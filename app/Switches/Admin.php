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

		if (! Option::get('admin_wordpress_logo')) {
			$hook->addAction('admin_bar_menu', static fn ($wpAdminBar) => $wpAdminBar->remove_node('wp-logo'), 99);
		}

		if (Option::get('account_menu_howdy')) {
			return;
		}

		$hook->addFilter('admin_bar_menu', function ($wpAdminBar) use ($hook): void {
			$hook->removeAction('admin_bar_menu', 'wp_admin_bar_my_account_item', 7);
			$hook->removeAction('admin_bar_menu', 'wp_admin_bar_my_account_item', PHP_INT_MAX);
			$this->addMyAccountMenu($wpAdminBar);
		}, PHP_INT_MAX);
	}

	public function addMyAccountMenu(WP_Admin_Bar $wpAdminBar): void
	{
		$currentUser = wp_get_current_user();
		$userId = get_current_user_id();
		$avatar = get_avatar($userId, 26);
		$class = ( $avatar ? 'with-avatar' : 'no-avatar' );

		$wpAdminBar->add_menu([
			'id'     => 'my-account',
			'parent' => 'top-secondary',
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress convention.
			'title'  => $currentUser->display_name . $avatar,
			'href'   => get_edit_profile_url($userId),
			'meta'   => ['class' => $class],
		]);
	}
}
