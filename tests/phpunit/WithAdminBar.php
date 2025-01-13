<?php

declare(strict_types=1);

namespace Syntatis\Tests;

use WP_Admin_Bar;

/**
 * @group feature-admin-bar
 * @group module-admin
 */
trait WithAdminBar
{
	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public static function set_up_before_class(): void
	{
		parent::set_up_before_class();

		require_once ABSPATH . WPINC . '/class-wp-admin-bar.php';
	}

	private static function setUpAdminBar(): void
	{
		$wpAdminBar = new WP_Admin_Bar();

		_wp_admin_bar_init();
		do_action_ref_array('admin_bar_menu', [&$wpAdminBar]);

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_admin_bar'] = $wpAdminBar;
	}

	private static function tearDownAdminBar(): void
	{
		unset($GLOBALS['wp_admin_bar']);
	}
}
