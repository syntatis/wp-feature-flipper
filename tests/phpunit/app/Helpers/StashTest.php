<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\Stash;
use Syntatis\Tests\WPTestCase;

class StashTest extends WPTestCase
{
	/**
	 * @dataProvider dataPatchArray
	 * @testdox should patch the option value
	 */
	public function testPatchArray(
		array $value,
		array $stashed,
		array $source,
		array $expect
	): void {
		update_option('_' . Option::name('baz') . '_stash', $stashed);

		$this->assertEquals($expect, Stash::patch('baz', $value, $source));
	}

	public function dataPatchArray(): iterable
	{
		yield [
			['a'], // Value.
			['a'], // Stashed.
			['b'], // Source.
			['b'], // Updated value.
		];

		yield [
			['a'], // Value.
			['a', 'b'], // Stashed.
			['c', 'd'], // Source.
			['c', 'd'], // Updated value.
		];

		yield [
			['a', 'b'], // Value.
			['a', 'b'], // Stashed.
			['c'], // Source.
			['c'], // Updated value.
		];

		yield [
			['a'], // Value.
			['a'], // Stashed.
			['a', 'b'], // Source.
			['a', 'b'],
		];

		yield [
			['a'], // Value.
			['a', 'b'], // Stashed.
			['a', 'b', 'c'], // Source.
			['a', 'c'], // Updated value.
		];

		yield [
			['a', 'b'], // Value.
			['a', 'b'], // Stashed.
			['a'], // Source.
			['a'], // Updated value.
		];

		yield [
			['a'], // Value.
			['a', 'b'], // Stashed.
			['a', 'c'], // Source.
			['a', 'c'], // Updated value.
		];

		yield [
			['a', 'b'], // Value.
			['a', 'b'], // Stashed.
			['a', 'c'], // Source.
			['a', 'c'], // Updated value.
		];

		yield [
			['a', 'b', 'c'], // Value.
			['a', 'b', 'c'], // Stashed.
			['a', 'b'], // Source.
			['a', 'b'], // Updated value.
		];

		yield [
			['a'], // Value.
			['a', 'b'], // Stashed.
			['a', 'b'], // Source.
			['a'], // Updated value.
		];

		yield [
			['a'], // Value.
			[], // Stashed.
			['a', 'd'], // Source.
			['a', 'd'], // Updated value.
		];

		yield [
			[], // Value.
			['a'], // Stashed.
			['a', 'd'], // Source.
			['d'], // Updated value.
		];

		yield [
			['a', 'b', 'c'], // Value.
			[], // Stashed.
			['a', 'b', 'c', 'd'], // Source.
			['a', 'b', 'c', 'd'], // Updated value.
		];
	}
}
