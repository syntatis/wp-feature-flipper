<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\AdminBar;
use Syntatis\FeatureFlipper\Features\AdminBar\RegisteredMenu;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WithAdminBar;
use Syntatis\Tests\WPTestCase;
use WP_Admin_Bar;

use function array_keys;
use function sort;

use const PHP_INT_MAX;

/**
 * @group feature-admin-bar
 * @group module-admin
 */
class AdminBarTest extends WPTestCase
{
	use WithAdminBar;

	private Hook $hook;
	private AdminBar $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		// Backup globals to restore them later.
		$this->hook = new Hook();
		$this->instance = new AdminBar();
		$this->instance->hook($this->hook);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		unset($_GET['tab']);
		self::tearDownAdminBar();

		parent::tear_down();
	}

	/** @testdox should has the callback attached to hook */
	public function testHook(): void
	{
		$this->assertSame(10, $this->hook->hasAction('syntatis/feature_flipper/updated_options', [$this->instance, 'stashOptions']));
		$this->assertSame(10, $this->hook->hasFilter('syntatis/feature_flipper/inline_data', [$this->instance, 'filterInlineData']));

		/**
		 * Test that callbacks should be attached to hooks with the highest priority,
		 * otherwise changes on the Admin Bar might not be properly applied.
		 */
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_bar_menu', [$this->instance, 'removeNodes']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('show_admin_bar', [$this->instance, 'showAdminBar']));
		$this->assertFalse($this->hook->hasAction('admin_bar_menu', [$this->instance, 'addMyAccountNode']));
		$this->assertFalse($this->hook->hasAction('admin_bar_menu', [$this->instance, 'addEnvironmentTypeNode']));

		Option::update('admin_bar_howdy', false);
		$this->instance->hook($this->hook);

		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_bar_menu', [$this->instance, 'addMyAccountNode']));
		$this->assertFalse($this->hook->hasAction('admin_bar_menu', [$this->instance, 'addEnvironmentTypeNode']));

		Option::update('admin_bar_env_type', true);
		$this->instance->hook($this->hook);

		$this->assertSame(10, $this->hook->hasAction('admin_bar_menu', [$this->instance, 'addEnvironmentTypeNode']));
	}

	/** @testdox should return the registered menu as the default */
	public function testOptionDefault(): void
	{
		$this->assertEmpty(array_keys(RegisteredMenu::all('top')));
		$this->assertEmpty(Option::get('admin_bar_menu'));

		self::setUpAdmin();

		$keys = array_keys(RegisteredMenu::all('top'));
		sort($keys);

		$this->assertNotEmpty($keys);
		$this->assertEquals($keys, Option::get('admin_bar_menu'));
	}

	/**
	 * @dataProvider dataOptionPatched
	 * @testdox should update the value and return with the patch
	 */
	public function testOptionPatched(array $menu): void
	{
		self::setUpAdmin();

		/** @var WP_Admin_Bar $wpAdminBar */
		$wpAdminBar = $GLOBALS['wp_admin_bar'];
		$wpAdminBar->add_node(['id' => 'baz', 'title' => 'Baz']);

		$this->assertTrue(Option::update('admin_bar_menu', $menu));
		$this->assertContains('baz', Option::get('admin_bar_menu'));
	}

	public static function dataOptionPatched(): iterable
	{
		yield [[]];
		yield [['wp-logo', 'comments']];
	}

	/**
	 * @dataProvider dataStashOptions
	 * @testdox should update the stash option
	 *
	 * @param mixed $expect The expected value returned from the stash option.
	 */
	public function testStashOptions(array $options, $expect): void
	{
		self::setUpAdmin();

		$this->instance->stashOptions($options);

		$this->assertSame($expect, get_option('_' . Option::name('admin_bar_menu') . '_stash'));
	}

	public static function dataStashOptions(): iterable
	{
		yield [
			[Option::name('admin_bar_menu')],
			[
				'comments',
				'customize', // Manually added on the list.
				'edit', // Manually added on the list.
				'new-content',
				'search', // Manually added on the list.
				'wp-logo',
			],
		];

		yield [[], false];
		yield [['admin_bar_menu'], false]; // Invalid option name.
	}

	/** @testdox should return the registered menu in admin for inline data */
	public function testFilterInlineData(): void
	{
		$_GET['tab'] = 'general';

		$data = $this->instance->filterInlineData([]);

		$this->assertEmpty($data);

		/**
		 * The `admin` tab is the only tab that should return the registered menu.
		 */
		$_GET['tab'] = 'admin';

		$data = $this->instance->filterInlineData([]);

		$this->assertSame([
			'$wp' => [
				'adminBarMenu' => array_keys(RegisteredMenu::all('top')),
			],
		], $data);
	}

	/** @testdox should remove nodes from the admin bar */
	public function testRemoveNodes(): void
	{
		self::setUpAdmin();

		/** @var WP_Admin_Bar $wpAdminBar */
		$wpAdminBar = $GLOBALS['wp_admin_bar'];
		$nodes = $wpAdminBar->get_nodes();

		$this->assertArrayHasKey('wp-logo', $nodes);
		$this->assertArrayHasKey('comments', $nodes);
		$this->assertArrayHasKey('new-content', $nodes);

		$this->assertTrue(Option::update('admin_bar_menu', [])); // Deselect all items.
		$this->assertTrue(Option::stash('admin_bar_menu', array_keys(RegisteredMenu::all('top')))); // After deselecting all items, stash should be updated.

		$this->instance->removeNodes($wpAdminBar);

		$nodes = $wpAdminBar->get_nodes();

		$this->assertArrayNotHasKey('wp-logo', $nodes);
		$this->assertArrayNotHasKey('comments', $nodes);
		$this->assertArrayNotHasKey('new-content', $nodes);
	}

	private static function setUpAdmin(): void
	{
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		set_current_screen('dashboard');
		self::setUpAdminBar();
	}
}
