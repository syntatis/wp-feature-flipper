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

	/**
	 * @dataProvider dataPatchArray
	 * @testdox should patch the option value
	 */
	public function testPatchArray(
		array $current,
		array $stashed,
		array $patchValue,
		array $expectCurrent
	): void {
		Option::update('baz', $current);
		Option::update('baz_stash', $stashed);
		OptionStash::patch('baz', $patchValue);

		$this->assertEquals($patchValue, OptionStash::get('baz'));
		$this->assertEquals($expectCurrent, Option::get('baz'));
	}

	public function dataPatchArray(): iterable
	{
		yield [
			['a'], // Current.
			['a'], // Stashed.
			['b'], // Patch.
			['b'], // Updated value.
		];

		yield [
			['a'], // Current.
			['a', 'b'], // Stashed.
			['c', 'd'], // Patch.
			['c', 'd'], // Updated value.
		];

		yield [
			['a', 'b'], // Current.
			['a', 'b'], // Stashed.
			['c'], // Patch.
			['c'], // Updated value.
		];

		yield [
			['a'], // Current.
			['a'], // Stashed.
			['a', 'b'], // Patch.
			['a', 'b'],
		];

		yield [
			['a'], // Current.
			['a', 'b'], // Stashed.
			['a', 'b', 'c'], // Patch.
			['a', 'c'], // Updated value.
		];

		yield [
			['a', 'b'], // Current.
			['a', 'b'], // Stashed.
			['a'], // Patch.
			['a'], // Updated value.
		];

		yield [
			['a'], // Current.
			['a', 'b'], // Stashed.
			['a', 'c'], // Patch.
			['a', 'c'], // Updated value.
		];

		yield [
			['a', 'b'], // Current.
			['a', 'b'], // Stashed.
			['a', 'c'], // Patch.
			['a', 'c'], // Updated value.
		];

		yield [
			['a', 'b', 'c'], // Current.
			['a', 'b', 'c'], // Stashed.
			['a', 'b'], // Patch.
			['a', 'b'], // Updated value.
		];

		yield [
			['a'], // Current.
			['a', 'b'], // Stashed.
			['a', 'b'], // Patch.
			['a'], // Updated value.
		];

		yield [
			['a'], // Current.
			[], // Stashed.
			['a', 'd'], // Patch.
			['a', 'd'], // Updated value.
		];

		yield [
			[], // Current.
			['a'], // Stashed.
			['a', 'd'], // Patch.
			['d'], // Updated value.
		];
	}
}
