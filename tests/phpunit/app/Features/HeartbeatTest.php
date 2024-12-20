<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Heartbeat;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-heartbeat
 * @group module-advanced
 */
class HeartbeatTest extends WPTestCase
{
	private Heartbeat $instance;
	private Hook $hook;

	// phpcs:ignore
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new Heartbeat();
		$this->instance->hook($this->hook);
	}

	public function testAdminIntervalDefault(): void
	{
		$this->assertSame(60, Option::get('heartbeat_admin_interval'));

		update_option(Option::name('heartbeat_admin'), false);

		$this->assertNull(Option::get('heartbeat_admin_interval'));
	}

	public function testAdminIntervalUpdated(): void
	{
		update_option(Option::name('heartbeat_admin_interval'), 240);

		$this->assertSame(240, Option::get('heartbeat_admin_interval'));

		update_option(Option::name('heartbeat_admin'), false);

		$this->assertNull(Option::get('heartbeat_admin_interval'));
	}

	public function testFrontIntervalDefault(): void
	{
		$this->assertSame(60, Option::get('heartbeat_front_interval'));

		update_option(Option::name('heartbeat_front'), false);

		$this->assertNull(Option::get('heartbeat_front_interval'));
	}

	public function testFrontIntervalUpdated(): void
	{
		update_option(Option::name('heartbeat_front_interval'), 120);

		$this->assertSame(120, Option::get('heartbeat_front_interval'));

		update_option(Option::name('heartbeat_front'), false);

		$this->assertNull(Option::get('heartbeat_front_interval'));
	}
}
