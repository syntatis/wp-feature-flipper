<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Heartbeat;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Heartbeat;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManageAdmin;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;
use WP_Scripts;

use function is_object;

use const PHP_INT_MAX;

/**
 * @group feature-heartbeat
 * @group module-advanced
 */
class ManageAdminTest extends WPTestCase
{
	/**
	 * Stores the original `WP_Scripts` instance.
	 */
	private ?WP_Scripts $wpScripts;
	private Hook $hook;
	private ManageAdmin $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function set_up(): void
	{
		parent::set_up();

		/**
		 * Some of the tests will modify the global `WP_Scripts`.
		 *
		 * Create the clone of the object to preserve and restore it once the test
		 * is done.
		 *
		 * @see \Syntatis\Tests\Features\Heartbeat\ManageAdminTest::tear_down(); The method where the $wp_scripts global is restored.
		 */
		$wpScripts = $GLOBALS['wp_scripts'] ?? null;
		$this->wpScripts = is_object($wpScripts) ? clone $wpScripts : null;

		$this->hook = new Hook();
		$this->instance = new ManageAdmin();
		$this->instance->hook($this->hook);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function tear_down(): void
	{
		parent::tear_down();

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_scripts'] = $this->wpScripts;
		unset($GLOBALS['pagenow']);
	}

	/** @testdox should has the callback attached to hook */
	public function testHook(): void
	{
		$instance = new ManageAdmin();
		$instance->hook($this->hook);

		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_init', [$instance, 'deregisterScripts']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('heartbeat_settings', [$instance, 'filterSettings']));
	}

	/** @testdox should return the default value */
	public function testOptionDefault(): void
	{
		$this->assertTrue(Option::isOn('heartbeat_admin'));
	}

	/** @testdox should return interval default value */
	public function testIntervalOptionDefault(): void
	{
		$this->assertSame(60, Option::get('heartbeat_admin_interval'));
	}

	/** @testdox should return updated value for "heartbeat_admin_interval" */
	public function testIntervalOptionUpdated(): void
	{
		Option::update('heartbeat_admin_interval', 240);

		$this->assertSame(240, Option::get('heartbeat_admin_interval'));
	}

	/** @testdox should affect "heartbeat_admin", not "heartbeat_admin_interval" option */
	public function testOptionsWhenGlobalOptionIsFalse(): void
	{
		Option::update('heartbeat', false);

		$this->assertFalse(
			Option::isOn('heartbeat_admin'),
			'Admin option should be false, since the global option is false.',
		);
		$this->assertSame(
			60,
			Option::get('heartbeat_admin_interval'),
			'Interval still return even if the global option is false.',
		);
	}

	/** @testdox should not affect "heartbeat_admin_interval" option */
	public function testIntervalOptionWhenAdminOptionIsFalse(): void
	{
		Option::update('heartbeat_admin', false);

		$this->assertSame(
			60,
			Option::get('heartbeat_admin_interval'),
			'Interval still return even if the admin option is false.',
		);
	}

	/**
	 * Test whether the "heartbeat_post_editor" option would affect "heartbeat_admin"
	 * and "heartbeat_admin_interval" options.
	 *
	 * @testdox should not affect "heartbeat_admin" and "heartbeat_admin_interval" options
	 */
	public function testOptionsWhenPostEditorOptionIsFalse(): void
	{
		Option::update('heartbeat_post_editor', false);

		$this->assertTrue(Option::isOn('heartbeat_admin'));
		$this->assertSame(60, Option::get('heartbeat_admin_interval'));
	}

	/**
	 * @dataProvider dataFilterSettingsOnAdminPage
	 * @testdox should change "interval" on the admin page
	 *
	 * @param mixed $value  The value to update the "heartbeat_admin_interval" option.
	 * @param mixed $expect The expected value returned.
	 */
	public function testFilterSettingsOnAdminPage($value, $expect): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'index.php'; // phpcs:ignore
		set_current_screen('dashboard');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		// Assert default.
		$this->assertSame(
			[
				'interval' => 60,
				'minimalInterval' => 60,
			],
			$this->instance->filterSettings([]),
		);

		// Update.
		Option::update('heartbeat_admin_interval', $value);

		// Assert.
		$this->assertSame($expect, $this->instance->filterSettings([]));
	}

	public static function dataFilterSettingsOnAdminPage(): iterable
	{
		yield [
			10,
			[
				'interval' => 10,
				'minimalInterval' => 10,
			],
		];

		yield [
			'10',
			[
				'interval' => 10,
				'minimalInterval' => 10,
			],
		];

		yield [
			10.2,
			[
				'interval' => 10,
				'minimalInterval' => 10,
			],
		];

		yield ['foo', []];
	}

	/** @testdox should not affect interval on the post editor */
	public function testFilterSettingsOnPostEditor(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post.php'; // phpcs:ignore
		set_current_screen('post.php');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		// Assert default.
		$this->assertSame([], $this->instance->filterSettings([]));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the admin pages
	 * when the "heartbeat_admin" option is set to `false`.
	 *
	 * @testdox should deregister the "heartbeat" script on the admin pages
	 */
	public function testDeregisterScriptsOnAdminPage(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'index.php'; // phpcs:ignore
		set_current_screen('dashboard');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$this->instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(Option::isOn('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		Option::update('heartbeat_admin', false);

		// Reload.
		$this->instance->deregisterScripts();

		// Assert.
		$this->assertFalse(Option::isOn('heartbeat_admin'));
		$this->assertFalse(wp_script_is('heartbeat', 'registered'));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the post editor
	 * when the "heartbeat_admin" option is set to `false`.
	 *
	 * @testdox should not deregister the "heartbeat" script on the post editor (post.php)
	 */
	public function testDeregisterScriptsOnPostEditor(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post.php'; // phpcs:ignore
		set_current_screen('post');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$this->instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(Option::isOn('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		Option::update('heartbeat_admin', false);

		// Reload.
		$this->instance->deregisterScripts();

		// Assert.
		$this->assertFalse(Option::isOn('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the new post editor
	 * when the "heartbeat_admin" option is set to `false`.
	 *
	 * @testdox should not deregister the "heartbeat" script on the post editor (post-new.php)
	 */
	public function testDeregisterScriptsOnPostNewEditor(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post.php'; // phpcs:ignore
		set_current_screen('post');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$this->instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(Option::isOn('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		Option::update('heartbeat_admin', false);

		// Reload.
		$this->instance->deregisterScripts();

		// Assert.
		$this->assertFalse(Option::isOn('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));
	}
}
