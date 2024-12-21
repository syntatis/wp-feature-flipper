<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Heartbeat;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Heartbeat;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManageFront;
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
class ManageFrontTest extends WPTestCase
{
	/**
	 * Stores the original `WP_Scripts` instance.
	 */
	private ?WP_Scripts $wpScripts;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function set_up(): void
	{
		parent::set_up();

		$wpScripts = $GLOBALS['wp_scripts'] ?? null;
		$this->wpScripts = is_object($wpScripts) ? clone $wpScripts : null;
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function tear_down(): void
	{
		parent::tear_down();

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_scripts'] = $this->wpScripts;
		unset($GLOBALS['pagenow']);
	}

	public function testHook(): void
	{
		$hook = new Hook();
		$instance = new ManageFront();
		$instance->hook($hook);

		$this->assertSame(PHP_INT_MAX, $hook->hasAction('admin_init', [$instance, 'deregisterScripts']));
		$this->assertSame(PHP_INT_MAX, $hook->hasFilter('heartbeat_settings', [$instance, 'filterSettings']));
	}

	/** @testdox should return `true` as the "heartbeat_front" default */
	public function testFrontOptionDefault(): void
	{
		$this->assertTrue(Option::get('heartbeat_front'));
	}

	/** @testdox should return `60` as the "heartbeat_front_interval" default */
	public function testFrontIntervalOptionDefault(): void
	{
		$this->assertTrue(Option::get('heartbeat_front'));
		$this->assertSame(60, Option::get('heartbeat_front_interval'));
	}

	/** @testdox should return updated value for "heartbeat_front_interval" */
	public function testFrontIntervalOptionUpdated(): void
	{
		update_option(Option::name('heartbeat_front_interval'), 120);

		$this->assertSame(120, Option::get('heartbeat_front_interval'));
	}

	/** @testdox should return `null` for "heartbeat_front_interval" when "heartbeat_front" is set to `false` */
	public function testFrontIntervalOptionNull(): void
	{
		update_option(Option::name('heartbeat_front'), false);

		$this->assertNull(Option::get('heartbeat_front_interval'));
	}

	/**
	 * Test whether the "heartbeat" global option would affect "hearbeat_front"
	 * and "heartbeat_front_interval" options.
	 *
	 * @testdox should return `false` and `null` for "heartbeat_front" and "heartbeat_front_interval" respectively
	 */
	public function testGlobalOption(): void
	{
		// Update the "heartbeat" global option.
		update_option(Option::name('heartbeat'), false);

		// Reload.
		$hook = new Hook();
		$instance = new ManageFront();
		$instance->hook($hook);

		$this->assertFalse(
			Option::get('heartbeat_front'),
			'Heartbeat front option should be false, since the global option is false.',
		);
		$this->assertNull(
			Option::get('heartbeat_front_interval'),
			'Heartbeat front interval should be null, since the global option is false.',
		);
	}

	/**
	 * Test whether the "heartbeat_admin" option would affect "hearbeat_front"
	 * and "heartbeat_front_interval" options.
	 *
	 * @testdox should not affect "heartbeat_front" and "heartbeat_front_interval" options
	 */
	public function testAdminOption(): void
	{
		update_option(Option::name('heartbeat_admin'), false);

		$hook = new Hook();
		$instance = new ManageFront();
		$instance->hook($hook);

		$this->assertTrue(Option::get('heartbeat_front'));
		$this->assertSame(60, Option::get('heartbeat_front_interval'));
	}

	/**
	 * Test whether the "heartbeat_post_editor" option would affect "hearbeat_front"
	 * and "heartbeat_front_interval" options.
	 *
	 * @testdox should not affect "heartbeat_front" and "heartbeat_front_interval" options
	 */
	public function testPostEditorOption(): void
	{
		update_option(Option::name('heartbeat_post_editor'), false);

		$hook = new Hook();
		$instance = new ManageFront();
		$instance->hook($hook);

		$this->assertTrue(Option::get('heartbeat_front'));
		$this->assertSame(60, Option::get('heartbeat_front_interval'));
	}

	/** @testdox should change "minimalInterval" on the front pages */
	public function testFilterSettingsOnFrontPages(): void
	{
		// Setup.
		$instance = new ManageFront();

		// Assert.
		$this->assertSame(60, $instance->filterSettings(['minimalInterval' => 60])['minimalInterval']);

		// Update.
		update_option(Option::name('heartbeat_front_interval'), 10);

		// Assert.
		$this->assertSame(
			10,
			$instance->filterSettings(['minimalInterval' => 10])['minimalInterval'],
			'The "minimalInterval" setting should be changed since it\'s not on the front end.',
		);
	}

	/** @testdox should not change "minimalInterval" on the admin pages */
	public function testFilterSettingsOnAdminPages(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'index.php'; // phpcs:ignore
		set_current_screen('dashboard');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		$instance = new ManageFront();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame(60, $instance->filterSettings(['minimalInterval' => 60])['minimalInterval']);

		// Update.
		update_option(Option::name('heartbeat_front_interval'), 10);

		// Assert.
		$this->assertSame(
			60,
			$instance->filterSettings(['minimalInterval' => 60])['minimalInterval'],
			'The "minimalInterval" setting should not be changed since it\'s not on the front end.',
		);
	}

	/** @testdox should not change "minimalInterval" on the post editor (post.php) */
	public function testFilterSettingsOnPostEditor(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post.php'; // phpcs:ignore
		set_current_screen('post');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		$instance = new ManageFront();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame(60, $instance->filterSettings(['minimalInterval' => 60])['minimalInterval']);

		// Update.
		update_option(Option::name('heartbeat_front_interval'), 10);

		// Assert.
		$this->assertSame(
			60,
			$instance->filterSettings(['minimalInterval' => 60])['minimalInterval'],
			'The "minimalInterval" setting should be changed since it\'s on the post editor.',
		);
	}

	/** @testdox should not change "minimalInterval" on the post editor (post-new.php) */
	public function testFilterSettingsOnPostNewEditor(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post-new.php'; // phpcs:ignore
		set_current_screen('posts-new.php');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		$instance = new ManageFront();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame(60, $instance->filterSettings(['minimalInterval' => 60])['minimalInterval']);

		// Update.
		update_option(Option::name('heartbeat_front_interval'), 10);

		// Assert.
		$this->assertSame(
			60,
			$instance->filterSettings(['minimalInterval' => 60])['minimalInterval'],
			'The "minimalInterval" setting should be changed since it\'s on the post editor.',
		);
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the front pages
	 * when the "heartbeat_front" option is set to `false`.
	 *
	 * @testdox should not deregister the "heartbeat" script on the front pages
	 */
	public function testDeregisterScriptsOnFrontPages(): void
	{
		// Setup.
		$instance = new ManageFront();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertFalse(is_admin());
		$this->assertTrue(Option::get('heartbeat_front'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_front'), false);

		// Reload.
		$instance = new ManagePostEditor();
		$instance->deregisterScripts();

		// Assert.
		$this->assertFalse(is_admin());
		$this->assertFalse(Option::get('heartbeat_front'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the admin pages
	 * when the "heartbeat_front" option is set to `false`.
	 *
	 * @testdox should not deregister the "heartbeat" script on the admin pages
	 */
	public function testDeregisterScriptsOnAdminPages(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'index.php'; // phpcs:ignore
		set_current_screen('dashboard');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$instance = new ManageFront();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(is_admin());
		$this->assertTrue(Option::get('heartbeat_front'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_front'), false);

		// Reload.
		$instance = new ManageFront();
		$instance->deregisterScripts();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertFalse(Option::get('heartbeat_front'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the post editor
	 * when the "heartbeat_front" option is set to `false`.
	 *
	 * @testdox should not deregister the "heartbeat" script on the post editor
	 */
	public function testDeregisterScriptsOnPostEditor(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post.php'; // phpcs:ignore
		set_current_screen('post');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$instance = new ManageFront();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(is_admin());
		$this->assertTrue(Option::get('heartbeat_front'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_front'), false);

		// Reload.
		$instance = new ManageFront();
		$instance->deregisterScripts();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertFalse(Option::get('heartbeat_front'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));
	}
}
