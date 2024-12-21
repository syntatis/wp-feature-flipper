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
		$instance = new ManagePostEditor();
		$instance->hook($hook);

		$this->assertSame(PHP_INT_MAX, $hook->hasAction('admin_init', [$instance, 'deregisterScripts']));
		$this->assertSame(PHP_INT_MAX, $hook->hasFilter('heartbeat_settings', [$instance, 'filterSettings']));
	}

	/** @testdox should return `15` as the "heartbeat_post_editor_interval" default */
	public function testPostEditorIntervalOptionDefault(): void
	{
		$this->assertTrue(Option::get('heartbeat_post_editor'));
		$this->assertSame(15, Option::get('heartbeat_post_editor_interval'));
	}

	/** @testdox should return null for "heartbeat_post_editor_interval" when "heartbeat_post_editor" is set to `false` */
	public function testPostEditorIntervalOptionNull(): void
	{
		update_option(Option::name('heartbeat_post_editor'), false);

		$this->assertNull(Option::get('heartbeat_post_editor_interval'));
	}

	/** @testdox should return updated value for "heartbeat_post_editor_interval" */
	public function testPostEditorIntervalOptionUpdated(): void
	{
		update_option(Option::name('heartbeat_post_editor_interval'), 30);

		$this->assertSame(30, Option::get('heartbeat_post_editor_interval'));
	}

	/**
	 * Test whether the "heartbeat" global option would affect "heartbeat_post_editor"
	 * and "heartbeat_post_editor_interval" options.
	 *
	 * @testdox should return `false` and `null` for "heartbeat_autosave" and "heartbeat_autosave_interval" respectively
	 */
	public function testGlobalOption(): void
	{
		// Update the "heartbeat" global option.
		update_option(Option::name('heartbeat'), false);

		// Reload.
		$hook = new Hook();
		$instance = new ManagePostEditor();
		$instance->hook($hook);

		$this->assertFalse(
			Option::get('heartbeat_post_editor'),
			'Heartbeat post editor option should be false, since the global option is false.',
		);
		$this->assertNull(
			Option::get('heartbeat_post_editor_interval'),
			'Heartbeat post editor interval should be null, since the global option is false.',
		);
	}

	/**
	 * Test whether the "heartbeat_admin" option would affect "heartbeat_front"
	 * and "heartbeat_post_editor" options.
	 *
	 * @testdox should not affect "heartbeat_post_editor" and "heartbeat_post_editor_interval" options
	 */
	public function testAdminOption(): void
	{
		update_option(Option::name('heartbeat_admin'), false);

		$hook = new Hook();
		$instance = new ManagePostEditor();
		$instance->hook($hook);

		$this->assertTrue(Option::get('heartbeat_post_editor'));
		$this->assertSame(15, Option::get('heartbeat_post_editor_interval'));
	}

	/**
	 * Test whether the "interval" setting is changed when it's on the post editor.
	 *
	 * @testdox should change the "interval" setting since it's on the post editor
	 */
	public function testFilterSettingsOnPostEditor(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post.php'; // phpcs:ignore
		set_current_screen('post.php');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$instance = new ManagePostEditor();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame(15, $instance->filterSettings(['interval' => 15])['interval']);

		// Update.
		update_option(Option::name('heartbeat_post_editor_interval'), 40);

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame(40, Option::get('heartbeat_post_editor_interval'));
		$this->assertSame(
			40,
			$instance->filterSettings(['interval' => 70])['interval'],
			'The "interval" setting should be changed since it\'s on post editor.',
		);
		$this->assertSame(
			40,
			$instance->filterSettings(['minimalInterval' => 70])['minimalInterval'],
			'The "minimalInterval" setting should be changed since it\'s on post editor.',
		);
	}

	/**
	 * Test whether the "interval" setting update with numeric string
	 *
	 * @testdox should update the "interval" setting with numeric string
	 */
	public function testFilterSettingsUpdateNumericString(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'post.php'; // phpcs:ignore
		set_current_screen('post.php');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$instance = new ManagePostEditor();

		// Update.
		update_option(Option::name('heartbeat_post_editor_interval'), '45');

		// Assert.
		$this->assertSame('45', Option::get('heartbeat_post_editor_interval'));
		$this->assertSame(
			45, // Casted to integer.
			$instance->filterSettings(['interval' => 70])['interval'],
			'The "interval" setting should be changed since it\'s on post editor.',
		);
		$this->assertSame(
			45, // Casted to integer.
			$instance->filterSettings(['minimalInterval' => 70])['minimalInterval'],
			'The "minimalInterval" setting should be changed since it\'s on post editor.',
		);
	}

	public function testFilterSettingsOnAdminPages(): void
	{
		// Setup.
		$GLOBALS['pagenow'] = 'index.php'; // phpcs:ignore
		set_current_screen('dashboard');
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));

		$instance = new ManagePostEditor();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertSame(70, $instance->filterSettings(['interval' => 70])['interval']);

		// Update.
		update_option(Option::name('heartbeat_post_editor_interval'), 50);

		// Assert.
		$this->assertSame(
			70,
			$instance->filterSettings(['interval' => 70])['interval'],
			'The "interval" setting should not be changed since it\'s not on post editor.',
		);
		$this->assertSame(
			70,
			$instance->filterSettings(['minimalInterval' => 70])['minimalInterval'],
			'The "minimalInterval" setting should not be changed since it\'s not on post editor.',
		);
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

		$instance = new ManagePostEditor();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(Option::get('heartbeat_post_editor'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_post_editor'), false);

		// Reload.
		$instance = new ManagePostEditor();
		$instance->deregisterScripts();

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

		$instance = new ManagePostEditor();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_post_editor'), false);

		// Reload.
		$instance = new ManagePostEditor();
		$instance->deregisterScripts();

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
		$instance = new ManagePostEditor();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertFalse(is_admin());
		$this->assertTrue(Option::get('heartbeat_post_editor'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_post_editor'), false);

		// Reload.
		$instance = new ManagePostEditor();
		$instance->deregisterScripts();

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

		$instance = new ManagePostEditor();
		$instance->deregisterScripts();

		// Assert default.
		$this->assertTrue(is_admin());
		$this->assertTrue(Option::get('heartbeat_post_editor'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));

		// Update.
		update_option(Option::name('heartbeat_post_editor'), false);

		// Reload.
		$instance = new ManagePostEditor();
		$instance->deregisterScripts();

		// Assert.
		$this->assertTrue(is_admin());
		$this->assertFalse(Option::get('heartbeat_post_editor'));
		$this->assertTrue(wp_script_is('heartbeat', 'registered'));
	}
}
