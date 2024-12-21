<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Heartbeat;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;
use WP_Scripts;

use function is_object;

use const PHP_INT_MAX;

/**
 * @group feature-heartbeat
 * @group module-advanced
 */
class HeartbeatTest extends WPTestCase
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
	}

	public function testHook(): void
	{
		// Setup.
		$hook = new Hook();
		$instance = new Heartbeat();
		$instance->hook($hook);

		$this->assertSame(PHP_INT_MAX, $hook->hasAction('init', [$instance, 'deregisterScripts']));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered when the
	 * "heartbeat" global option is set to `false`.
	 *
	 * @testdox should deregister the "heartbeat" script
	 */
	public function testDeregisterScripts(): void
	{
		// Setup.
		$heartbeat = new Heartbeat();
		$heartbeat->deregisterScripts();

		// Assert default.
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update the "heartbeat" global option.
		update_option(Option::name('heartbeat'), false);

		// Reload.
		$heartbeat = new Heartbeat();
		$heartbeat->deregisterScripts();

		// Assert.
		$this->assertFalse(
			wp_script_is('heartbeat', 'registered'),
			'Heartbeat script should not be registered, since the global option is false.',
		);
	}

	/**
	 * Test whether the "heartbeat" script is deregistered when the
	 * "heartbeat_admin" option is set to `false`.
	 *
	 * @testdox should not deregister the "heartbeat" script when the "heartbeat_admin" option is set to `false`
	 */
	public function testDeregisterScriptsAdminOption(): void
	{
		// Setup.
		$heartbeat = new Heartbeat();
		$heartbeat->deregisterScripts();

		// Assert default.
		$this->assertTrue(Option::get('heartbeat_admin'));

		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update the "heartbeat_admin" option.
		update_option(Option::name('heartbeat_admin'), false);

		// Reload.
		$heartbeat = new Heartbeat();
		$heartbeat->deregisterScripts();

		// Assert.
		$this->assertTrue(
			wp_script_is('heartbeat', 'registered'),
			'Heartbeat script should still be registered, since the global option true.',
		);
	}

	/**
	 * Test whether the "heartbeat" global option would affect "heartbeat_admin"
	 * and "heartbeat_admin_interval" options.
	 *
	 * @testdox should return `false` and `null` for "heartbeat_admin" and "heartbeat_admin_interval" respectively
	 */
	public function testAdminOption(): void
	{
		// Default.
		$this->assertTrue(Option::get('heartbeat_admin'));
		$this->assertSame(60, Option::get('heartbeat_admin_interval'));

		// Update the "heartbeat" global option.
		update_option(Option::name('heartbeat'), false);

		// Reload.
		$hook = new Hook();
		$instance = new Heartbeat();
		$instance->hook($hook);

		$this->assertFalse(
			Option::get('heartbeat_admin'),
			'Heartbeat admin option should be false, since the global option is false.',
		);
		$this->assertNull(
			Option::get('heartbeat_admin_interval'),
			'Heartbeat admin  interval should be null, since the global option is false.',
		);
	}

	/** @testdox should return `60` as the "heartbeat_admin_interval" default */
	public function testAdminIntervalOptionDefault(): void
	{
		$this->assertSame(60, Option::get('heartbeat_admin_interval'));
	}

	/** @testdox should return updated value for "heartbeat_admin_interval" */
	public function testAdminIntervalOptionUpdated(): void
	{
		update_option(Option::name('heartbeat_admin_interval'), 240);

		$this->assertSame(240, Option::get('heartbeat_admin_interval'));
	}

	/** @testdox should return `null` for "heartbeat_admin_interval" when "heartbeat_admin" is set to `false` */
	public function testAdminIntervalOptionNull(): void
	{
		update_option(Option::name('heartbeat_admin'), false);

		$this->assertNull(Option::get('heartbeat_admin_interval'));
	}
}
