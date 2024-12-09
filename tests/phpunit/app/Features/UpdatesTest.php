<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

/** @group feature-updates */
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

	public function testGlobalUpdates(): void
	{
		$this->assertTrue(Option::get('updates'));

		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::get('updates'));
	}

	/** @group feature-auto-update */
	public function testGlobalAutoUpdate(): void
	{
		$this->assertTrue(Option::get('auto_updates'));

		update_option(Option::name('auto_updates'), false);

		$this->assertFalse(Option::get('auto_updates'));
	}

	public function testCoreUpdatesDefault(): void
	{
		$this->assertTrue(Option::get('update_core'));
	}

	public function testCoreUpdatesDisabled(): void
	{
		update_option(Option::name('update_core'), false);

		$this->assertFalse(Option::get('update_core'));
	}

	public function testCoreUpdatesGlobalDisabled(): void
	{
		$this->assertTrue(Option::get('update_core'));

		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::get('update_core'));
	}

	/** @group feature-auto-update */
	public function testCoreAutoUpdateDefault(): void
	{
		$this->assertTrue(Option::get('auto_update_core'));
	}

	/** @group feature-auto-update */
	public function testCoreAutoUpdateDisabled(): void
	{
		update_option(Option::name('auto_update_core'), false);

		$this->assertFalse(Option::get('auto_update_core'));
	}

	/** @group feature-auto-update */
	public function testCoreAutoUpdateGlobalAutoUpdateDisabled(): void
	{
		$this->assertTrue(Option::get('auto_update_core'));

		update_option(Option::name('auto_updates'), false);

		$this->assertFalse(Option::get('auto_update_core'));
	}

	/** @group feature-auto-update */
	public function testCoreAutoUpdateGlobalUpdatesDisabled(): void
	{
		$this->assertTrue(Option::get('auto_update_core'));

		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::get('auto_update_core'));
	}

	public function testPluginsUpdatesDefault(): void
	{
		$this->assertTrue(Option::get('update_plugins'));
	}

	public function testPluginsUpdatesDisabled(): void
	{
		update_option(Option::name('update_plugins'), false);

		$this->assertFalse(Option::get('update_plugins'));
	}

	public function testPluginsUpdatesGlobalUpdatesDisabled(): void
	{
		$this->assertTrue(Option::get('update_plugins'));

		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::get('update_plugins'));
	}

	/** @group feature-auto-update */
	public function testPluginsAutoUpdateDefault(): void
	{
		$this->assertTrue(Option::get('auto_update_plugins'));
	}

	/** @group feature-auto-update */
	public function testPluginsAutoUpdateDisabled(): void
	{
		update_option(Option::name('auto_update_plugins'), false);

		$this->assertFalse(Option::get('auto_update_plugins'));
	}

	/** @group feature-auto-update */
	public function testPluginsAutoUpdateGlobalAutoUpdateDisabled(): void
	{
		$this->assertTrue(Option::get('auto_update_plugins'));

		update_option(Option::name('auto_updates'), false);

		$this->assertFalse(Option::get('auto_update_plugins'));
	}

	/** @group feature-auto-update */
	public function testPluginsAutoUpdateGlobalUpdatesDisabled(): void
	{
		$this->assertTrue(Option::get('auto_update_plugins'));

		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::get('auto_update_plugins'));
	}

	public function testThemesUpdatesDefault(): void
	{
		$this->assertTrue(Option::get('update_themes'));
	}

	public function testThemesUpdatesDisabled(): void
	{
		update_option(Option::name('update_themes'), false);

		$this->assertFalse(Option::get('update_themes'));
	}

	public function testThemesUpdatesGlobalDisabled(): void
	{
		$this->assertTrue(Option::get('update_themes'));

		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::get('update_themes'));
	}

	/** @group feature-auto-update */
	public function testThemesAutoUpdateDefault(): void
	{
		$this->assertTrue(Option::get('auto_update_themes'));
	}

	/** @group feature-auto-update */
	public function testThemesAutoUpdateDisabled(): void
	{
		update_option(Option::name('auto_update_themes'), false);

		$this->assertFalse(Option::get('auto_update_themes'));
	}

	/** @group feature-auto-update */
	public function testThemesAutoUpdateGlobalAutoUpdateDisabled(): void
	{
		$this->assertTrue(Option::get('auto_update_themes'));

		update_option(Option::name('auto_updates'), false);

		$this->assertFalse(Option::get('auto_update_themes'));
	}

	/** @group feature-auto-update */
	public function testThemesAutoUpdateGlobalUpdatesDisabled(): void
	{
		$this->assertTrue(Option::get('auto_update_themes'));

		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::get('auto_update_themes'));
	}
}
