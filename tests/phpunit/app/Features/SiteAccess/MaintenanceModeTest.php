<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\SiteAccess;

use SSFV\Codex\Foundation\Hooks\Hook;
use stdClass;
use Syntatis\FeatureFlipper\Features\SiteAccess\MaintenanceMode;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;
use WP_Admin_Bar;

use function trim;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * @group feature-site-access
 * @group module-site
 */
class MaintenanceModeTest extends WPTestCase
{
	private MaintenanceMode $instance;
	private Hook $hook;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public static function set_up_before_class(): void
	{
		parent::set_up_before_class();

		require_once ABSPATH . WPINC . '/class-wp-admin-bar.php';
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new MaintenanceMode();
		$this->instance->hook($this->hook);
	}

	/** @testdox should has the callback attached to hook */
	public function testHook(): void
	{
		$this->assertSame(
			PHP_INT_MAX,
			$this->hook->hasFilter(Option::hook('sanitize:site_maintenance_args'), [$this->instance, 'sanitizeArgsOption']),
		);
		$this->assertFalse($this->hook->hasAction('admin_bar_menu', [$this->instance, 'adminBarMenu']));
		$this->assertFalse($this->hook->hasAction('rightnow_end', [$this->instance, 'showRightNowStatus']));
		$this->assertFalse($this->hook->hasAction('template_redirect', [$this->instance, 'forceMaintenance']));
		$this->assertFalse($this->hook->hasFilter('wp_title', [$this->instance, 'filterPageTitle']));

		Option::update('site_access', 'maintenance');
		$this->instance->hook($this->hook);

		$this->assertSame(
			PHP_INT_MAX,
			$this->hook->hasFilter(Option::hook('sanitize:site_maintenance_args'), [$this->instance, 'sanitizeArgsOption']),
		);
		$this->assertSame(
			PHP_INT_MIN,
			$this->hook->hasAction('admin_bar_menu', [$this->instance, 'adminBarMenu']),
		);
		$this->assertSame(
			PHP_INT_MIN,
			$this->hook->hasAction('rightnow_end', [$this->instance, 'showRightNowStatus']),
		);
		$this->assertSame(
			PHP_INT_MIN,
			$this->hook->hasAction('template_redirect', [$this->instance, 'templateRedirect']),
		);
		$this->assertSame(
			PHP_INT_MAX,
			$this->hook->hasFilter('wp_title', [$this->instance, 'filterPageTitle']),
		);
	}

	/**
	 * @dataProvider dataSanitizeArgsOption
	 *
	 * @param mixed               $value    The value to sanitize.
	 * @param array<string,mixed> $expected The expected returned value.
	 */
	public function testSanitizeArgsOption($value, array $expected): void
	{
		$this->assertSame($expected, $this->instance->sanitizeArgsOption($value));
	}

	public static function dataSanitizeArgsOption(): iterable
	{
		yield [
			new stdClass(),
			[
				'headline' => '',
				'message' => '',
			],
		];

		yield [
			[],
			[
				'headline' => '',
				'message' => '',
			],
		];

		yield [
			['foo' => 'bar'],
			[
				'headline' => '',
				'message' => '',
			],
		];

		yield [
			['headline' => '<div>Hello</div> <foo>World</foo>'],
			[
				'headline' => 'Hello World',
				'message' => '',
			],
		];

		yield [
			[
				'headline' => '<div>Hello</div> <foo>World</foo>',
				'message' => '<p>This is a description</p>',
			],
			[
				'headline' => 'Hello World',
				'message' => 'This is a description',
			],
		];
	}

	/** @testdox should note add the maintenance mode to the admin bar */
	public function testAdminBarMenuNonAdmin(): void
	{
		$wpAdminBar = new WP_Admin_Bar();

		$this->instance->adminBarMenu($wpAdminBar);

		$this->assertFalse(is_admin());
		$this->assertEmpty($wpAdminBar->get_nodes());
	}

	/** @testdox should add the maintenance mode to the admin bar */
	public function testAdminBarMenuAdmin(): void
	{
		$wpAdminbar = new WP_Admin_Bar();

		set_current_screen('dashboard');
		wp_set_current_user($this->factory()->user->create(['role' => 'administrator']));

		$this->instance->adminBarMenu($wpAdminbar);

		$nodes = $wpAdminbar->get_nodes();

		$this->assertTrue(is_admin());
		$this->assertArrayHasKey('syntatis-feature-flipper-site-access', $nodes);

		$node = $nodes['syntatis-feature-flipper-site-access'];

		$this->assertSame('Maintenance', trim(wp_strip_all_tags($node->title)));
		$this->assertSame('top-secondary', $node->parent);
		$this->assertStringEndsWith('/wp-admin/options-general.php?tab=site&page=syntatis-feature-flipper', $node->href);
	}

	/** @testdox should note add the maintenance mode with the href on the admin bar */
	public function testAdminBarMenuAuthor(): void
	{
		$wpAdminbar = new WP_Admin_Bar();

		set_current_screen('dashboard');
		wp_set_current_user($this->factory()->user->create(['role' => 'author']));

		$this->instance->adminBarMenu($wpAdminbar);

		$nodes = $wpAdminbar->get_nodes();

		$this->assertTrue(is_admin());
		$this->assertArrayHasKey('syntatis-feature-flipper-site-access', $nodes);

		$node = $nodes['syntatis-feature-flipper-site-access'];

		$this->assertSame('Maintenance', trim(wp_strip_all_tags($node->title)));
		$this->assertSame('top-secondary', $node->parent);
		$this->assertFalse($node->href);
	}
}
