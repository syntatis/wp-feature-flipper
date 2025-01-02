<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use Syntatis\FeatureFlipper\Helpers\AutoUpdate;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-updates
 * @group module-advanced
 */
class AutoUpdateTest extends WPTestCase
{
	/** @testdox should return inherited value */
	public function testGlobal(): void
	{
		$this->assertTrue(Option::isOn('updates'));

		$this->assertTrue(AutoUpdate::global()->isEnabled(true));
		$this->assertFalse(AutoUpdate::global()->isEnabled(false));

		// Disable global updates.
		update_option(Option::name('updates'), false);

		// Should return false when global updates are disabled.
		$this->assertFalse(Option::isOn('updates'));
		$this->assertFalse(AutoUpdate::global()->isEnabled(true));
	}

	/** @testdox should return core inherited value */
	public function testCore(): void
	{
		$this->assertTrue(AutoUpdate::core()->isEnabled(true));
		$this->assertFalse(AutoUpdate::core()->isEnabled(false));
	}

	/** @testdox should return `false` when global "updates" is disabled */
	public function testCoreWhenGlobalUpdatesIsFalse(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::isOn('updates'));
		$this->assertFalse(AutoUpdate::core()->isEnabled(true));
	}

	/** @testdox should return `false` when global "auto_updates" is `false` */
	public function testCoreWhenGlobalAutoUpdatesIsFalse(): void
	{
		// Disable global auto-update.
		update_option(Option::name('auto_updates'), false);

		$this->assertTrue(Option::isOn('updates'));
		$this->assertFalse(Option::isOn('auto_updates'));
		$this->assertFalse(AutoUpdate::core()->isEnabled(true));
	}

	/** @testdox should return plugins inherited value */
	public function testPlugins(): void
	{
		$this->assertTrue(AutoUpdate::plugins()->isEnabled(true));
		$this->assertFalse(AutoUpdate::plugins()->isEnabled(false));
	}

	/** @testdox should return `false` when global "updates" is `false` */
	public function testPluginsWhenGlobalUpdatesIsFalse(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::isOn('updates'));
		$this->assertFalse(AutoUpdate::plugins()->isEnabled(true));
	}

	/** @testdox should return `false` when global "auto_updates" is `false` */
	public function testPluginsWhenGlobalAutoUpdatesIsFalse(): void
	{
		// Disable global auto-update.
		update_option(Option::name('auto_updates'), false);

		$this->assertFalse(AutoUpdate::plugins()->isEnabled(true));
	}

	/** @testdox should return themes inherited value */
	public function testThemes(): void
	{
		$this->assertTrue(AutoUpdate::themes()->isEnabled(true));
		$this->assertFalse(AutoUpdate::themes()->isEnabled(false));
	}

	/** @testdox should return `false` when global "updates" is `false` */
	public function testThemesWhenGlobalUpdatesIsFalse(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		$this->assertFalse(AutoUpdate::themes()->isEnabled(true));
	}

	/** @testdox should return `false` when global "auto_updates" is `false` */
	public function testThemesGlobalAutoUpdateDisabled(): void
	{
		// Disable global auto-update.
		update_option(Option::name('auto_updates'), false);

		$this->assertFalse(AutoUpdate::themes()->isEnabled(true));
	}
}
