<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates\Helpers;

use Syntatis\FeatureFlipper\Features\Updates\Helpers\AutoUpdate;
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

		$this->assertTrue(AutoUpdate::global(true)->isEnabled());
		$this->assertFalse(AutoUpdate::global(false)->isEnabled());

		// Disable global updates.
		Option::update('updates', false);

		// Should return `false` when global updates are disabled.
		$this->assertFalse(Option::isOn('updates'));
		$this->assertFalse(AutoUpdate::global(true)->isEnabled());
	}

	/** @testdox should return components inherited value */
	public function testComponents(): void
	{
		$this->assertTrue(AutoUpdate::components(true)->isEnabled());
		$this->assertFalse(AutoUpdate::components(false)->isEnabled());
	}

	/** @testdox should return `false` when global "updates" is disabled */
	public function testComponentsWhenGlobalUpdatesIsFalse(): void
	{
		// Disable global updates.
		Option::update('updates', false);

		$this->assertFalse(Option::isOn('updates'));
		$this->assertFalse(AutoUpdate::components(true)->isEnabled());
	}
}
