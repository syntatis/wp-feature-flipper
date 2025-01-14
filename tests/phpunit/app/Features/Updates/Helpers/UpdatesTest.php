<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates\Helpers;

use Syntatis\FeatureFlipper\Features\Updates\Helpers\Updates;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-updates
 * @group module-advanced
 */
class UpdatesTest extends WPTestCase
{
	/** @testdox should return global inherited value */
	public function testGlobal(): void
	{
		$this->assertTrue(Updates::global(true)->isEnabled());
		$this->assertFalse(Updates::global(false)->isEnabled());
	}

	/** @testdox should return components inherited value */
	public function testComponents(): void
	{
		$this->assertTrue(Updates::components(true)->isEnabled());
		$this->assertFalse(Updates::components(false)->isEnabled());
	}

	/** @testdox should return `false` when global "updates" is disabled */
	public function testComponentsWhenGlobalUpdatesIsFalse(): void
	{
		// Disable global updates.
		Option::update('updates', false);

		$this->assertFalse(Updates::components(true)->isEnabled());
	}
}
