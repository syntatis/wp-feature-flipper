<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\DashboardWidgets;
use Syntatis\FeatureFlipper\Helpers\Admin;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\InlineData;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;

/**
 * @group feature-dashboard-widgets
 * @group module-admin
 */
class DashboardWidgetsTest extends WPTestCase
{
	private Hook $hook;
	private DashboardWidgets $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new DashboardWidgets();
		$this->instance->hook($this->hook);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		unset($GLOBALS['wp_meta_boxes']['dashboard']);

		parent::tear_down();
	}

	/** @testdox should have callback attached to hooks */
	public function testHook(): void
	{
		$this->assertSame(
			10,
			$this->hook->hasAction(
				'syntatis/feature_flipper/updated_options',
				[$this->instance, 'stashOptions'],
			),
		);
		$this->assertSame(
			10,
			$this->hook->hasFilter(
				'syntatis/feature_flipper/inline_data',
				[$this->instance, 'filterInlineData'],
			),
		);
		$this->assertSame(
			PHP_INT_MAX,
			$this->hook->hasAction(
				'wp_dashboard_setup',
				[$this->instance, 'setup'],
			),
		);
	}

	/**
	 * @requires PHP >= 8.0
	 * @testdox should return empty when loaded outside the setting screen or dashboard
	 */
	public function testOptionDefault(): void
	{
		$this->assertSame([], Option::get('dashboard_widgets_enabled'));
	}

	/** @testdox should return the list of dashboard widgets on the setting page */
	public function testOptionDefaultOnPluginSettingScreen(): void
	{
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		set_current_screen('settings_page_' . App::name());

		$this->assertTrue(Admin::isScreen('settings_page_' . App::name()));
		$this->assertSame(
			[
				'dashboard_activity',
				'dashboard_right_now',
				'dashboard_quick_press',
				'dashboard_site_health',
				'dashboard_primary',
			],
			Option::get('dashboard_widgets_enabled'),
		);
	}

	/**
	 * @requires PHP 7.4
	 * @testdox should return the list of dashboard widgets on the setting page
	 */
	public function testOptionDefaultOnPluginSettingScreenPhp74(): void
	{
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		set_current_screen('settings_page_' . App::name());

		$this->assertTrue(Admin::isScreen('settings_page_' . App::name()));
		$this->assertSame(
			[
				'dashboard_activity',
				'dashboard_right_now',
				'dashboard_php_nag',
				'dashboard_quick_press',
				'dashboard_site_health',
				'dashboard_primary',
			],
			Option::get('dashboard_widgets_enabled'),
		);
	}

	/**
	 * @requires PHP >= 8.0
	 * @testdox should return the list of dashboard widgets on the dashboard screen
	 */
	public function testOptionDefaultOnDashboardScreen(): void
	{
		require_once ABSPATH . '/wp-admin/includes/dashboard.php';

		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		set_current_screen('dashboard');
		wp_dashboard_setup();

		$this->assertTrue(Admin::isScreen('dashboard'));
		$this->assertSame(
			[
				'dashboard_activity',
				'dashboard_right_now',
				'dashboard_quick_press',
				'dashboard_site_health',
				'dashboard_primary',
			],
			Option::get('dashboard_widgets_enabled'),
		);
	}

	/**
	 * @requires PHP 7.4
	 * @testdox should return the list of dashboard widgets on the dashboard screen
	 */
	public function testOptionDefaultOnDashboardScreenPhp74(): void
	{
		require_once ABSPATH . '/wp-admin/includes/dashboard.php';

		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		set_current_screen('dashboard');
		wp_dashboard_setup();

		$this->assertTrue(Admin::isScreen('dashboard'));
		$this->assertSame(
			[
				'dashboard_activity',
				'dashboard_right_now',
				'dashboard_php_nag',
				'dashboard_quick_press',
				'dashboard_site_health',
				'dashboard_primary',
			],
			Option::get('dashboard_widgets_enabled'),
		);
	}

	/** @testdox should return the list of dashboard widgets on the other admin screen */
	public function testOptionDefaultOnOtherAdminScreen(): void
	{
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		set_current_screen('edit-post');

		$this->assertFalse(Admin::isScreen('dashboard'));
		$this->assertFalse(Admin::isScreen(App::name()));
		$this->assertSame([], Option::get('dashboard_widgets_enabled'));
	}

	/** @testdox should return the registered menu in admin for inline data */
	public function testFilterInlineData(): void
	{
		$_GET['tab'] = 'general';

		$data = $this->instance->filterInlineData(new InlineData());

		$this->assertFalse(isset($data['$wp']['dashboardWidgets']));
	}
}
