<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use SSFV\Codex\Facades\App;
use Syntatis\FeatureFlipper\Helpers\Admin;
use Syntatis\Tests\WPTestCase;

class AdminTest extends WPTestCase
{
	/**
	 * @dataProvider dataUrl
	 * @testdox should return the admin url
	 */
	public function testUrl(string $address, array $args, string $expect): void
	{
		$this->assertStringEndsWith($expect, Admin::url($address, $args));
	}

	public static function dataUrl(): iterable
	{
		yield [
			App::name(),
			[],
			'/wp-admin/options-general.php?page=' . App::name(),
		];

		yield [
			App::name(),
			['tab' => 'general'],
			'/wp-admin/options-general.php?tab=general&page=' . App::name(),
		];

		yield [
			'options-general.php',
			[],
			'/wp-admin/options-general.php',
		];

		yield [
			'options-general.php',
			['tab' => 'general'],
			'/wp-admin/options-general.php?tab=general',
		];

		yield [
			'options-permalink.php',
			[],
			'/wp-admin/options-permalink.php',
		];
	}
}
