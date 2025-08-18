<?php

declare(strict_types=1);

namespace Syntatis\Tests\Helpers;

use SFFV\Codex\Facades\App;
use Syntatis\FeatureFlipper\Helpers\Admin;
use Syntatis\Tests\WPTestCase;

class AdminTest extends WPTestCase
{
	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		unset($GLOBALS['pagenow']);

		parent::tear_down();
	}

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

	/** @testdox should return true if the current screen is the provided address */
	public function testIsScreenApp(): void
	{
		$this->assertFalse(Admin::isScreen(App::name()));

		wp_set_current_user($this->factory()->user->create(['role' => 'administrator']));

		$this->assertFalse(Admin::isScreen(App::name()));

		set_current_screen('settings_page_' . App::name());

		$this->assertTrue(Admin::isScreen(App::name()));
	}

	/** @testdox should return `true` when the address ends with ".php" */
	public function testIsScreenPageNow(): void
	{
		wp_set_current_user($this->factory()->user->create(['role' => 'administrator']));
		set_current_screen('options-permalink');

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Testing requires global variable.
		$GLOBALS['pagenow'] = 'options-permalink.php';

		$this->assertTrue(Admin::isScreen('options-permalink.php'));
	}

	/** @testdox should return `true` with the screen id */
	public function testIsScreenId(): void
	{
		wp_set_current_user($this->factory()->user->create(['role' => 'administrator']));
		set_current_screen('dashboard');

		$this->assertTrue(Admin::isScreen('dashboard'));
	}
}
