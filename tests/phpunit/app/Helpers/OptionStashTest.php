<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\OptionStash;
use Syntatis\Tests\WPTestCase;

class OptionStashTest extends WPTestCase
{
	/** @testdox should return the option name with the prefix */
	public function testGet(): void
	{
		update_option('syntatis_feature_flipper_foo_stash', ['foo']);

		$this->assertEquals(['foo'], OptionStash::get('foo'));
	}

	/** @testdox should return the option name with the prefix */
	public function testDelete(): void
	{
		update_option('syntatis_feature_flipper_bar_stash', ['bar']);

		$this->assertTrue(OptionStash::delete('bar'));
	}
}
