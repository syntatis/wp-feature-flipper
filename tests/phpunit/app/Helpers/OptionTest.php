<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

class OptionTest extends WPTestCase
{
	/** @testdox should return the option name with the prefix */
	public function testName(): void
	{
		$this->assertEquals('syntatis_feature_flipper_foo', Option::name('foo'));
	}

	/** @testdox should return `false` when the option does not exist */
	public function testGet(): void
	{
		$this->assertFalse(Option::isOn('foo'));
	}

	/** @testdox should return the value that's been updated */
	public function testGetAfterUpdate(): void
	{
		update_option('syntatis_feature_flipper_foo', 'bar');

		$this->assertEquals('bar', Option::get('foo'));
	}
}
