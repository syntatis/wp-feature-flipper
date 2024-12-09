<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use Syntatis\FeatureFlipper\Helpers\AutoUpdate;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-updates
 * @group feature-auto-update
 */
class AutoUpdateTest extends WPTestCase
{
	public function testGlobal(): void
	{
		$this->assertTrue(AutoUpdate::global()->isEnabled(true));
		$this->assertFalse(AutoUpdate::global()->isEnabled(false));
	}

	public function testCore(): void
	{
		$this->assertTrue(AutoUpdate::core()->isEnabled(true));
		$this->assertFalse(AutoUpdate::core()->isEnabled(false));
	}

	public function testCoreGlobalUpdatesDisabled(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		// Should return false when global updates are disabled, even if core update is enabled.
		$this->assertFalse(AutoUpdate::core()->isEnabled(true));
	}

	public function testCoreGlobalAutoUpdateDisabled(): void
	{
		// Disable global auto-update.
		update_option(Option::name('auto_updates'), false);

		// Should return false when global auto-update is disabled, even if core update is enabled.
		$this->assertFalse(AutoUpdate::core()->isEnabled(true));
	}

	public function testPlugins(): void
	{
		$this->assertTrue(AutoUpdate::plugins()->isEnabled(true));
		$this->assertFalse(AutoUpdate::plugins()->isEnabled(false));
	}

	public function testPluginsGlobaleUpdatesDisabled(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		// Should return false when global updates are disabled, even if plugins updates are enabled.
		$this->assertFalse(AutoUpdate::plugins()->isEnabled(true));
	}

	public function testPluginsGlobalAutoUpdateDisabled(): void
	{
		// Disable global auto-update.
		update_option(Option::name('auto_updates'), false);

		// Should return false when global auto-update is disabled, even if plugins updates are enabled.
		$this->assertFalse(AutoUpdate::plugins()->isEnabled(true));
	}

	public function testThemes(): void
	{
		$this->assertTrue(AutoUpdate::themes()->isEnabled(true));
		$this->assertFalse(AutoUpdate::themes()->isEnabled(false));
	}

	public function testThemesGlobalUpdatesDisabled(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		// Should return false when global updates are disabled, even if themes updates are enabled.
		$this->assertFalse(AutoUpdate::themes()->isEnabled(true));
	}

	public function testThemesGlobalAutoUpdateDisabled(): void
	{
		// Disable global auto-update.
		update_option(Option::name('auto_updates'), false);

		// Should return false when global auto-update is disabled, even if themes updates are enabled.
		$this->assertFalse(AutoUpdate::themes()->isEnabled(true));
	}
}
