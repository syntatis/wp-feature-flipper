<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;

/**
 * @group feature-updates
 * @group module-advanced
 */
class UpdatesTest extends WPTestCase
{
	private Updates $instance;
	private Hook $hook;

	// phpcs:ignore
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new Updates();
		$this->instance->hook($this->hook);
	}

	/** @testdox should has the callback attached to hook */
	public function testHook(): void
	{
		// Updates related hooks.
		$this->assertFalse($this->hook->hasAction('admin_menu', [$this->instance, 'removeUpdateAdminMenu']));
		$this->assertSame(10, $this->hook->hasAction('init', 'wp_schedule_update_checks'));

		// Auto-updates related hooks.
		$this->assertFalse($this->hook->hasFilter('auto_update_translation', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('automatic_updater_disabled', '__return_false'));
		$this->assertSame(10, $this->hook->hasAction('wp_maybe_auto_update', 'wp_maybe_auto_update'));

		update_option(Option::name('updates'), false);
		update_option(Option::name('auto_updates'), false);

		// Reload.
		$this->instance->hook($this->hook);

		// Updates related hooks.
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_menu', [$this->instance, 'removeUpdateAdminMenu']));
		$this->assertFalse($this->hook->hasAction('init', 'wp_schedule_update_checks'));

		// Auto-updates related hooks.
		$this->assertSame(10, $this->hook->hasFilter('auto_update_translation', '__return_false'));
		$this->assertSame(10, $this->hook->hasFilter('automatic_updater_disabled', '__return_false'));
		$this->assertFalse($this->hook->hasAction('wp_maybe_auto_update', 'wp_maybe_auto_update'));
	}

	/** @testdox should return default values */
	public function testOptionsDefault(): void
	{
		$this->assertTrue(Option::get('updates'));
		$this->assertTrue(Option::get('auto_updates'));
	}

	/** @testdox should return updated values */
	public function testOptionsUpdated(): void
	{
		update_option(Option::name('updates'), false);
		update_option(Option::name('auto_updates'), false);

		$this->assertFalse(Option::get('updates'));
		$this->assertFalse(Option::get('auto_updates'));
	}

	/** @testdox should return `false` for `auto_updates` when `updates` option is `false` */
	public function testAutoUpdateWhenMainOptionIsFalse(): void
	{
		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::get('updates'));
		$this->assertFalse(Option::get('auto_updates'));
	}

	// public function testThemesUpdatesDefault(): void
	// {
	// 	$this->assertTrue(Option::get('update_themes'));
	// }

	// public function testThemesUpdatesDisabled(): void
	// {
	// 	update_option(Option::name('update_themes'), false);

	// 	$this->assertFalse(Option::get('update_themes'));
	// }

	// public function testThemesUpdatesGlobalDisabled(): void
	// {
	// 	$this->assertTrue(Option::get('update_themes'));

	// 	update_option(Option::name('updates'), false);

	// 	$this->assertFalse(Option::get('update_themes'));
	// }

	// /** @group feature-auto-update */
	// public function testThemesAutoUpdateDefault(): void
	// {
	// 	$this->assertTrue(Option::get('auto_update_themes'));
	// }

	// /** @group feature-auto-update */
	// public function testThemesAutoUpdateDisabled(): void
	// {
	// 	update_option(Option::name('auto_update_themes'), false);

	// 	$this->assertFalse(Option::get('auto_update_themes'));
	// }

	// /** @group feature-auto-update */
	// public function testThemesAutoUpdateGlobalAutoUpdateDisabled(): void
	// {
	// 	$this->assertTrue(Option::get('auto_update_themes'));

	// 	update_option(Option::name('auto_updates'), false);

	// 	$this->assertFalse(Option::get('auto_update_themes'));
	// }

	// /** @group feature-auto-update */
	// public function testThemesAutoUpdateGlobalUpdatesDisabled(): void
	// {
	// 	$this->assertTrue(Option::get('auto_update_themes'));

	// 	update_option(Option::name('updates'), false);

	// 	$this->assertFalse(Option::get('auto_update_themes'));
	// }
}
