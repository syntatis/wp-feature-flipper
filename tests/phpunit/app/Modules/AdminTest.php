<?php

declare(strict_types=1);

namespace Syntatis\Tests\Modules;

use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\Admin;
use Syntatis\Tests\WPTestCase;

use function version_compare;

/** @group module-admin */
class AdminTest extends WPTestCase
{
	private Hook $hook;
	private Admin $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		if (! version_compare($GLOBALS['wp_version'], '7.0', '>=')) {
			$this->markTestSkipped('This test requires WordPress 7.0 or newer.');
		}

		$this->hook = new Hook();
		$this->instance = new Admin();
		$this->instance->hook($this->hook);
	}

	/** @testdox should default to enabled */
	public function testDefaultOption(): void
	{
		$this->assertTrue(Option::isOn('admin_view_transitions'));
	}

	/** @testdox should not dequeue the style when enabled */
	public function testEnabled(): void
	{
		$this->assertFalse(
			$this->hook->hasAction(
				'admin_enqueue_scripts',
				[$this->instance, 'disableViewTransitions'],
			),
		);
	}

	/** @testdox should register the dequeue callback when disabled */
	public function testDisabled(): void
	{
		Option::update('admin_view_transitions', false);

		$this->instance->hook($this->hook);

		$this->assertSame(
			11,
			$this->hook->hasAction(
				'admin_enqueue_scripts',
				[$this->instance, 'disableViewTransitions'],
			),
		);
	}
}
