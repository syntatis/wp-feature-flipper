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
		$instance = new ManageAdmin();
		$instance->hook($hook);

		$this->assertSame(PHP_INT_MAX, $hook->hasAction('admin_init', [$instance, 'deregisterScripts']));
		$this->assertSame(PHP_INT_MAX, $hook->hasFilter('heartbeat_settings', [$instance, 'filterSettings']));
	}

	/** @testdox should return `true` as the "heartbeat_admin" default */
	public function testAdminOptionDefault(): void
	{
		$this->assertTrue(Option::get('heartbeat_admin'));
	}

	/** @testdox should return `60` as the "heartbeat_admin_interval" default */
	public function testAdminIntervalOptionDefault(): void
	{
		$this->assertSame(60, Option::get('heartbeat_admin_interval'));
	}

	/** @testdox should return updated value for "heartbeat_admin_interval" */
	public function testAdminIntervalOptionUpdated(): void
	{
		update_option(Option::name('heartbeat_admin_interval'), 240);

		$this->assertSame(240, Option::get('heartbeat_admin_interval'));
	}

	/** @testdox should return `null` for "heartbeat_admin_interval" when "heartbeat_admin" is set to `false` */
	public function testAdminIntervalOptionNull(): void
	{
		update_option(Option::name('heartbeat_admin'), false);

		$this->assertNull(Option::get('heartbeat_admin_interval'));
	}

	/**
	 * Test whether the "heartbeat" global option would affect "heartbeat_admin"
	 * and "heartbeat_admin_interval" options.
	 *
	 * @testdox should return `false` and `null` for "heartbeat_autosave" and "heartbeat_autosave_interval" respectively
	 */
	public function testGlobalOption(): void
	{
		// Update the "heartbeat" global option.
		update_option(Option::name('heartbeat'), false);

		// Reload.
		$hook = new Hook();
		$instance = new ManageAdmin();
		$instance->hook($hook);

		$this->assertFalse(
			Option::get('heartbeat_admin'),
			'Heartbeat admin option should be false, since the global option is false.',
		);
		$this->assertNull(
			Option::get('heartbeat_admin_interval'),
			'Heartbeat admin interval should be null, since the global option is false.',
		);
	}

	/**
	 * Test whether the "heartbeat_front" option would affect "heartbeat_admin"
	 * and "heartbeat_admin_interval" options.
	 *
	 * @testdox should not affect "heartbeat_admin" and "heartbeat_admin_interval" options
	 */
	public function tesFrontOption(): void
	{
		update_option(Option::name('heartbeat_front'), false);

		$hook = new Hook();
		$instance = new ManageAdmin();
		$instance->hook($hook);

		$this->assertTrue(Option::get('heartbeat_admin'));
		$this->assertSame(60, Option::get('heartbeat_admin_interval'));
	}

	/**
	 * Test whether the "heartbeat_post_editor" option would affect "heartbeat_admin"
	 * and "heartbeat_admin_interval" options.
	 *
	 * @testdox should not affect "heartbeat_admin" and "heartbeat_admin_interval" options
	 */
	public function testPostEditorOption(): void
	{
		update_option(Option::name('heartbeat_post_editor'), false);

		$hook = new Hook();
		$instance = new ManageAdmin();
		$instance->hook($hook);

		$this->assertTrue(Option::get('heartbeat_admin'));
		$this->assertSame(60, Option::get('heartbeat_admin_interval'));
	}

	/** @testdox should change "minimalInterval" on the admin pages */
	public function testFilterSettingsOnAdminPages(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'index.php'; // phpcs:ignore
		set_current_screen('dashboard');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		$instance = new ManageAdmin();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame(60, $instance->filterSettings(['minimalInterval' => 60])['minimalInterval']);

		// Update.
		update_option(Option::name('heartbeat_admin_interval'), 10);

		// Assert.
		$this->assertSame(
			10,
			$instance->filterSettings(['minimalInterval' => 10])['minimalInterval'],
			'The "minimalInterval" setting should be changed since it\'s on the admin.',
		);
	}

	/** @testdox should not change "minimalInterval" on the front pages */
	public function testFilterSettingsOnFrontPages(): void
	{
		// Setup.
		$instance = new ManageAdmin();

		// Assert.
		$this->assertSame(60, $instance->filterSettings(['minimalInterval' => 60])['minimalInterval']);

		// Update.
		update_option(Option::name('heartbeat_admin_interval'), 10);

		// Assert.
		$this->assertSame(
			60,
			$instance->filterSettings(['minimalInterval' => 60])['minimalInterval'],
			'The "minimalInterval" setting should not be changed since it\'s not on the admin.',
		);
	}

	public function testFilterSettingsOnPostEditor(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post.php'; // phpcs:ignore
		set_current_screen('post.php');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$instance = new ManageAdmin();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame(60, $instance->filterSettings(['minimalInterval' => 60])['minimalInterval']);

		// Update.
		update_option(Option::name('heartbeat_admin_interval'), 20);

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame(20, Option::get('heartbeat_admin_interval'));
		$this->assertSame(
			60,
			$instance->filterSettings(['minimalInterval' => 60])['minimalInterval'],
			'The "minimalInterval" setting should not be changed since it\'s on post editor.',
		);
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the admin pages
	 * when the "heartbeat_admin" option is set to `false`.
	 *
	 * @testdox should deregister the "heartbeat" script on the admin pages
	 */
	public function testDeregisterScriptsOnAdminPages(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'index.php'; // phpcs:ignore
		set_current_screen('dashboard');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$instance = new ManageAdmin();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(is_admin());
		$this->assertTrue(Option::get('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_admin'), false);

		// Reload.
		$instance = new ManageAdmin();
		$instance->deregisterScripts();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertFalse(Option::get('heartbeat_admin'));
		$this->assertFalse(wp_script_is('heartbeat', 'registered'));
	}

	/**
	 * Test whether the "heartbeat" script is deregistered on the front pages
	 * when the "heartbeat_admin" option is set to `false`.
	 *
	 * @testdox should not deregister the "heartbeat" script on the front pages
	 */
	public function testDeregisterScriptsOnFrontPages(): void
	{
		// Setup.
		$instance = new ManageAdmin();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertFalse(is_admin());
		$this->assertTrue(Option::get('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_admin'), false);

		// Reload.
		$instance = new ManageAdmin();
		$instance->deregisterScripts();

		// Assert.
		$this->assertFalse(is_admin());
		$this->assertFalse(Option::get('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));
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

		$instance = new ManageAdmin();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(is_admin());
		$this->assertTrue(Option::get('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_admin'), false);

		// Reload.
		$instance = new ManageAdmin();
		$instance->deregisterScripts();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertFalse(Option::get('heartbeat_admin'));
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

		$instance = new ManageAdmin();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(is_admin());
		$this->assertTrue(Option::get('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_admin'), false);

		// Reload.
		$instance = new ManageAdmin();
		$instance->deregisterScripts();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertFalse(Option::get('heartbeat_admin'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));
	}
}
