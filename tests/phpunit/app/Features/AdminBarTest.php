<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\AdminBar;
use Syntatis\FeatureFlipper\Features\AdminBar\RegisteredMenu;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use function array_keys;

use const PHP_INT_MAX;

/**
 * @group feature-admin-bar
 * @group module-admin
 */
class AdminBarTest extends WPTestCase
{
	private Hook $hook;
	private AdminBar $instance;

	/** @var array<string,mixed> */
	private array $globals = [];

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->globals = $GLOBALS;
		$this->hook = new Hook();
		$this->instance = new AdminBar();
		$this->instance->hook($this->hook);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		parent::tear_down();

		if (! isset($this->globals['wp_admin_bar'])) {
			return;
		}

		$GLOBALS['wp_admin_bar'] = $this->globals['wp_admin_bar']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
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

	/**
	 * @dataProvider dataStashOptions
	 * @testdox should update the stash option
	 *
	 * @param mixed $expect The expected value returned from the stash option.
	 */
	public function testStashOptions(array $options, $expect): void
	{
		$this->instance->stashOptions($options);

		$this->assertSame(
			get_option('_' . Option::name('admin_bar_menu') . '_stash'),
			$expect,
		);
	}

	public static function dataStashOptions(): iterable
	{
		yield [
			[Option::name('admin_bar_menu')],
			array_keys(RegisteredMenu::all('top')),
		];

		yield [[], false];
		yield [['admin_bar_menu'], false]; // Invalid option name.
	}
}
