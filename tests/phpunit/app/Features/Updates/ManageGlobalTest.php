<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Features\Updates\ManageGlobal;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;

/** @group feature-updates */
class ManageGlobalTest extends WPTestCase
{
	private ManageGlobal $instance;
	private Hook $hook;

	// phpcs:ignore
	public function set_up(): void
	{
		parent::set_up();

		$this->instance = new ManageGlobal();
		$this->hook = new Hook();
	}

	public function testHookUpdatesDisabled(): void
	{
		$this->assertFalse($this->hook->hasAction('admin_menu', '#features/updates/manage_global/remove_update_core_submenu'));
		$this->assertSame(10, $this->hook->hasAction('init', 'wp_schedule_update_checks'));

		update_option(Option::name('updates'), false);

		$this->instance->hook($this->hook);

		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_menu', '#features/updates/manage_global/remove_update_core_submenu'));
		$this->assertFalse($this->hook->hasAction('init', 'wp_schedule_update_checks'));
	}

	public function testHookAutoUpdatesDisabled(): void
	{
		$this->assertFalse($this->hook->hasFilter('auto_update_translation', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('automatic_updater_disabled', '__return_false'));
		$this->assertSame(10, $this->hook->hasAction('wp_maybe_auto_update', 'wp_maybe_auto_update'));

		update_option(Option::name('auto_updates'), false);

		$this->instance->hook($this->hook);

		$this->assertSame(10, $this->hook->hasFilter('auto_update_translation', '__return_false'));
		$this->assertSame(10, $this->hook->hasFilter('automatic_updater_disabled', '__return_false'));
		$this->assertFalse($this->hook->hasAction('wp_maybe_auto_update', 'wp_maybe_auto_update'));
	}
}
