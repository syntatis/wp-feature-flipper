<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use InvalidArgumentException;
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

	/**
	 * @dataProvider dataPatchArray
	 * @testdox should patch the option value
	 */
	public function testPatchArray(
		array $value,
		array $stash,
		array $source,
		array $expect
	): void {
		$this->assertTrue(Option::stash('baz', $stash));
		$this->assertEquals($expect, Option::patch('baz', $value, $source));
	}

	public function dataPatchArray(): iterable
	{
		/**
		 * Test where the stashed value is set, and the source value is different
		 * set of values. This situation could happen when the stashed option
		 * somehow failed to be updated.
		 */
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
			['a', 'b', 'c'], // Value.
			['a', 'b'], // Stashed.
			['a', 'b'], // Source.
			['a', 'b'], // Updated value.
		];

		/**
		 * When the value is empty, it could mean users may have unchecked all the
		 * options. Existing value should remain, while new value in the source
		 * should be included.
		 */
		yield [
			[], // Value.
			['a'], // Stashed.
			['a', 'd'], // Source.
			['d'], // Updated value.
		];

		yield [
			[], // Value.
			['a'], // Stashed.
			['a'], // Source.
			[], // Updated value.
		];

		/**
		 * The value should not change when the stashed value and the source are
		 * the same.
		 */
		yield [
			['a', 'b'], // Value.
			['a', 'b', 'c', 'd'], // Stashed.
			['a', 'b', 'c', 'd'], // Source.
			['a', 'b'], // Updated value.
		];

		/**
		 * Test where the stashed value is empty while the option value is already
		 * set. This situation could happen on site that's already been with an
		 * older version of the plugin, where stash option is not yet added,
		 * and has only been updated to the latest version.
		 */
		yield [
			['a', 'b', 'c'], // Value.
			[], // Stashed.
			['a', 'b', 'c', 'd'], // Source.
			['a', 'b', 'c', 'd'], // Updated value.
		];
	}

	/**
	 * @dataProvider dataHook
	 * @testdox should return the option name filter, added with plugin the prefix
	 */
	public function testHook(string $name, string $expect): void
	{
		$this->assertSame($expect, Option::hook($name));
	}

	public static function dataHook(): iterable
	{
		yield ['foo', 'option_syntatis_feature_flipper_foo'];
		yield ['default:foo', 'default_option_syntatis_feature_flipper_foo'];
		yield ['update:foo', 'update_option_syntatis_feature_flipper_foo'];
		yield ['delete:foo', 'delete_option_syntatis_feature_flipper_foo'];
		yield ['add:foo', 'add_option_syntatis_feature_flipper_foo'];
		yield ['sanitize:foo', 'sanitize_option_syntatis_feature_flipper_foo'];
	}

	/**
	 * @dataProvider dataHookInvalid
	 * @testdox should throw an exception when the hook name is invalid
	 */
	public function testHookInvalid(string $name): void
	{
		$this->expectException(InvalidArgumentException::class);

		Option::hook($name);
	}

	public static function dataHookInvalid(): iterable
	{
		yield ['bar:foo'];
		yield [':foo'];
		yield ['bar:'];
	}
}
