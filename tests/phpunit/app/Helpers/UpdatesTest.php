<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use Syntatis\FeatureFlipper\Helpers\Updates;
use Syntatis\FeatureFlipper\Option;
use Syntatis\Tests\WPTestCase;

/** @group feature-updates */
class UpdatesTest extends WPTestCase
{
	public function testGlobal(): void
	{
		$this->assertTrue(Updates::global()->isEnabled(true));
		$this->assertFalse(Updates::global()->isEnabled(false));
	}

	public function testCore(): void
	{
		$this->assertTrue(Updates::core()->isEnabled(true));
		$this->assertFalse(Updates::core()->isEnabled(false));
	}

	public function testCoreGlobalUpdatesDisabled(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		// Should return false when all updates are disabled, even if core updates are enabled.
		$this->assertFalse(Updates::core()->isEnabled(true));
	}

	public function testPlugins(): void
	{
		$this->assertTrue(Updates::plugins()->isEnabled(true));
		$this->assertFalse(Updates::plugins()->isEnabled(false));
	}

	public function testPluginsGlobalUpdatesDisabled(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		// Should return false when all updates are disabled, even if core updates are enabled.
		$this->assertFalse(Updates::plugins()->isEnabled(true));
	}

	public function testThemes(): void
	{
		$this->assertTrue(Updates::themes()->isEnabled(true));
		$this->assertFalse(Updates::themes()->isEnabled(false));
	}

	public function testThemesGlobalUpdatesDisabled(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		// Should return false when all updates are disabled, even if themes updates are enabled.
		$this->assertFalse(Updates::themes()->isEnabled(true));
	}
}
