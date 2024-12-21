<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Heartbeat;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Heartbeat;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManageAdmin;
use Syntatis\Tests\WPTestCase;
use WP_Scripts;

use function is_object;

use const PHP_INT_MAX;

/**
 * @group feature-heartbeat
 * @group module-advanced
 */
class ManageAdminTest extends WPTestCase
{
	/**
	 * Stores the original `WP_Scripts` instance.
	 */
	private ?WP_Scripts $wpScripts;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function set_up(): void
	{
		parent::set_up();

		$wpScripts = $GLOBALS['wp_scripts'] ?? null;
		$this->wpScripts = is_object($wpScripts) ? clone $wpScripts : null;
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function tear_down(): void
	{
		parent::tear_down();

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_scripts'] = $this->wpScripts;
		unset($GLOBALS['pagenow']);
	}

	public function testHook(): void
	{
		$hook = new Hook();
		$instance = new ManageAdmin();
		$instance->hook($hook);

		$this->assertSame(PHP_INT_MAX, $hook->hasAction('admin_init', [$instance, 'deregisterScripts']));
		$this->assertSame(PHP_INT_MAX, $hook->hasFilter('heartbeat_settings', [$instance, 'getSettings']));
	}
}
