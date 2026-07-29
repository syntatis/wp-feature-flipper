<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Heartbeat;

use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Heartbeat\Heartbeat;
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
	private Hook $hook;
	private Heartbeat $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function set_up(): void
	{
		parent::set_up();

		$wpScripts = $GLOBALS['wp_scripts'] ?? null;
		$this->wpScripts = is_object($wpScripts) ? clone $wpScripts : null;

		$this->hook = new Hook();
		$this->instance = new Heartbeat();
		$this->instance->hook($this->hook);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function tear_down(): void
	{
		parent::tear_down();

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_scripts'] = $this->wpScripts;
	}

	/** @testdox should have the callback attached to hook */
	public function testHook(): void
	{
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('init', [$this->instance, 'deregisterScripts']));
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
		$this->instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update the "heartbeat" global option.
		Option::update('heartbeat', false);

		// Reload.
		$this->instance->deregisterScripts();

		// Assert.
		$this->assertFalse(
			wp_script_is('heartbeat', 'registered'),
			'Heartbeat script should not be registered, since the global option is false.',
		);
	}
}
