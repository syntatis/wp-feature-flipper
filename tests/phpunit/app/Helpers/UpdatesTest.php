<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\Updates;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-updates
 * @group module-advanced
 */
class UpdatesTest extends WPTestCase
{
	/** @testdox should return global inherted value */
	public function testGlobal(): void
	{
		$this->assertTrue(Updates::global()->isEnabled(true));
		$this->assertFalse(Updates::global()->isEnabled(false));
	}

	/** @testdox should return core inherited value */
	public function testCore(): void
	{
		$this->assertTrue(Updates::core()->isEnabled(true));
		$this->assertFalse(Updates::core()->isEnabled(false));
	}

	/** @testdox should return `false` when global "updates" is disabled */
	public function testCoreWhenGlobalUpdatesIsFalse(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		$this->assertFalse(Updates::core()->isEnabled(true));
	}

	/** @testdox should return plugins inherited value */
	public function testPlugins(): void
	{
		$this->assertTrue(Updates::plugins()->isEnabled(true));
		$this->assertFalse(Updates::plugins()->isEnabled(false));
	}

	/** @testdox should return `false` when global "updates" is disabled */
	public function testPluginsGlobalUpdatesDisabled(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		$this->assertFalse(Updates::plugins()->isEnabled(true));
	}

	/** @testdox should return themes inherited value */
	public function testThemes(): void
	{
		$this->assertTrue(Updates::themes()->isEnabled(true));
		$this->assertFalse(Updates::themes()->isEnabled(false));
	}

	/** @testdox should return `false` when global "updates" is disabled */
	public function testThemesGlobalUpdatesDisabled(): void
	{
		// Disable global updates.
		update_option(Option::name('updates'), false);

		$this->assertFalse(Updates::themes()->isEnabled(true));
	}
}
