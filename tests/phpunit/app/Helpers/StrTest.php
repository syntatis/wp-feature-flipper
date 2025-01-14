<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use Syntatis\FeatureFlipper\Helpers\Str;
use Syntatis\Tests\WPTestCase;

class StrTest extends WPTestCase
{
	/**
	 * @dataProvider dataEndsWith
	 * @testdox should return `true` if the string ends with the given value
	 */
	public function testEndsWith(string $haystack, string $needle): void
	{
		$this->assertTrue(Str::endsWith($haystack, $needle));
	}

	public static function dataEndsWith(): iterable
	{
		yield ['', ''];
		yield ['foo', ''];
		yield ['foo', 'o'];
		yield ['foo', 'foo'];
		yield ['\x00', '\x00'];
		yield ['\x00', ''];
		yield ['a\x00', '\x00'];
		yield ['ab\x00c', 'b\x00c'];
		yield ['අයේෂ්', 'ෂ්'];
		yield ['අයේෂ්', '්'];
		yield ['🙌🎉✨🚀', '🚀'];
	}

	/**
	 * @dataProvider dataNotEndsWith
	 * @testdox should return `false` if the string does not end with the given value
	 */
	public function testNotEndsWith(string $haystack, string $needle): void
	{
		$this->assertFalse(Str::endsWith($haystack, $needle));
	}

	public static function dataNotEndsWith(): iterable
	{
		yield ['foo', 'bar'];
		yield ['foo', 'oof'];
		yield ['foo', 'f'];
		yield ['අයේෂ්', 'ෂ'];
		yield ['foo', '\x00'];
		yield ['a\x00b', 'a\x00z'];
		yield ['a\x00b', 'd\x00b'];
		yield ['a', '\x00a'];
		yield ['foo', '🚀'];
		yield ['🙌🎉✨🚀', '✨'];
	}
}
