<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Heartbeat;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Heartbeat;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManagePostEditor;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;
use WP_Scripts;

use function is_object;

use const PHP_INT_MAX;

/**
 * @group feature-heartbeat
 * @group module-advanced
 */
class ManagePostEditorTest extends WPTestCase
{
	/**
	 * Stores the original `WP_Scripts` instance.
	 */
	private ?WP_Scripts $wpScripts;
	private Hook $hook;
	private ManagePostEditor $instance;

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
		 * @see \Syntatis\Tests\Features\Heartbeat\ManagePostEditorTest::tear_down(); The method where the $wp_scripts global is restored.
		 */
		$wpScripts = $GLOBALS['wp_scripts'] ?? null;
		$this->wpScripts = is_object($wpScripts) ? clone $wpScripts : null;

		$this->hook = new Hook();
		$this->instance = new ManagePostEditor();
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
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_init', [$this->instance, 'deregisterScripts']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('heartbeat_settings', [$this->instance, 'filterSettings']));
	}

	/** @testdox should return the default value */
	public function testOptionDefault(): void
	{
		$this->assertTrue(Option::get('heartbeat_post_editor'));
	}

	/** @testdox should return the interval default value */
	public function testIntervalOptionDefault(): void
	{
		$this->assertSame(15, Option::get('heartbeat_post_editor_interval'));
	}

	/** @testdox should return updated value for "heartbeat_post_editor_interval" */
	public function testIntervalOptionUpdated(): void
	{
		update_option(Option::name('heartbeat_post_editor_interval'), 30);

		$this->assertSame(30, Option::get('heartbeat_post_editor_interval'));
	}

	/**
	 * Test whether the "heartbeat" global option would affect "heartbeat_post_editor"
	 * and "heartbeat_post_editor_interval" options.
	 *
	 * @testdox should affect "heartbeat_post_editor", not "heartbeat_post_editor_interval" option
	 */
	public function testOptionsWhenGlobalOptionIsFalse(): void
	{
		update_option(Option::name('heartbeat'), false);

		$this->assertFalse(
			Option::get('heartbeat_post_editor'),
			'Heartbeat post editor option should be false, since the global option is false.',
		);
		$this->assertSame(
			15,
			Option::get('heartbeat_post_editor_interval'),
			'Heartbeat post editor interval should be returned, even if the global option is false.',
		);
	}

	/** @testdox should not affect "heartbeat_post_editor_interval" option  */
	public function testIntervalOptionWhenPostEditorOptionIsFalse(): void
	{
		update_option(Option::name('heartbeat_post_editor'), false);

		$this->assertSame(15, Option::get('heartbeat_post_editor_interval'));
	}

	/**
	 * Test whether the "heartbeat_admin" option would affect "heartbeat_front"
	 * and "heartbeat_post_editor" options.
	 *
	 * @testdox should not affect "heartbeat_post_editor" and "heartbeat_post_editor_interval" options
	 */
	public function testOptionsWhenAdminOptionIsFalse(): void
	{
		update_option(Option::name('heartbeat_admin'), false);

		$this->assertTrue(Option::get('heartbeat_post_editor'));
		$this->assertSame(15, Option::get('heartbeat_post_editor_interval'));
	}

	/**
	 * @dataProvider dataFilterSettingsOnPostEditor
	 * @testdox should change the "interval" setting since it's on the post editor
	 *
	 * @param mixed $value  The value to update the "heartbeat_post_editor" option.
	 * @param mixed $expect The expected value returned.
	 */
	public function testFilterSettingsOnPostEditor($value, $expect): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post.php'; // phpcs:ignore
		set_current_screen('post.php');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		// Assert default.
		$this->assertTrue(is_admin());
		$this->assertSame(
			[
				'interval' => 15,
				'minimalInterval' => 15,
			],
			$this->instance->filterSettings([]),
		);

		// Update.
		update_option(Option::name('heartbeat_post_editor_interval'), 40);

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame(
			[
				'interval' => 40,
				'minimalInterval' => 40,
			],
			$this->instance->filterSettings([]),
		);
	}

	public static function dataFilterSettingsOnPostEditor(): iterable
	{
		yield [
			30,
			[
				'interval' => 30,
				'minimalInterval' => 30,
			],
		];

		yield [
			'30',
			[
				'interval' => 30,
				'minimalInterval' => 30,
			],
		];

		yield [
			30.3,
			[
				'interval' => 30,
				'minimalInterval' => 30,
			],
		];

		yield ['foo', []];
	}

	/** @testdox should not affect interval on the admin page */
	public function testFilterSettingsOnAdminPage(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'index.php'; // phpcs:ignore
		set_current_screen('dashboard');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame([], $this->instance->filterSettings([]));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the post editor
	 * when the "heartbeat_post_editor" option is set to `false`.
	 *
	 * @testdox should deregister the "heartbeat" script on the post editor (post.php)
	 */
	public function testDeregisterScriptsOnPostEditor(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post.php'; // phpcs:ignore
		set_current_screen('post.php');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$this->instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(Option::get('heartbeat_post_editor'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_post_editor'), false);

		// Reload.
		$this->instance->deregisterScripts();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertFalse(Option::get('heartbeat_post_editor'));
		$this->assertFalse(wp_script_is('heartbeat', 'registered'));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the post editor
	 * (post-new.php) when the "heartbeat_post_editor" option is set to
	 * `false`.
	 *
	 * @testdox should deregister the "heartbeat" script on the post editor (post-new.php)
	 */
	public function testDeregisterScriptsOnPostNewEditor(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post-new.php'; // phpcs:ignore
		set_current_screen('post-new.php');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$this->instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_post_editor'), false);

		// Reload.
		$this->instance->deregisterScripts();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertFalse(Option::get('heartbeat_post_editor'));
		$this->assertFalse(wp_script_is('heartbeat', 'registered'));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the front pages
	 * when the "heartbeat_post_editor" option is set to `false`.
	 *
	 * @testdox should not deregister the "heartbeat" script on the front pages
	 */
	public function testDeregisterScriptsOnFrontPage(): void
	{
		// Setup.
		$this->instance->deregisterScripts();

		// Assert default.
		$this->assertFalse(is_admin());
		$this->assertTrue(Option::get('heartbeat_post_editor'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_post_editor'), false);

		// Reload.
		$this->instance->deregisterScripts();

		// Assert.
		$this->assertFalse(is_admin());
		$this->assertFalse(Option::get('heartbeat_post_editor'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the admin pages
	 * when the "heartbeat_post_editor" option is set to `false`.
	 *
	 * @testdox should not deregister the "heartbeat" script on the admin pages
	 */
	public function testDeregisterScriptsOnAdminPage(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'index.php'; // phpcs:ignore
		set_current_screen('dashboard');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$this->instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(is_admin());
		$this->assertTrue(Option::get('heartbeat_post_editor'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_post_editor'), false);

		// Reload.
		$this->instance->deregisterScripts();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertFalse(Option::get('heartbeat_post_editor'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));
	}
}
