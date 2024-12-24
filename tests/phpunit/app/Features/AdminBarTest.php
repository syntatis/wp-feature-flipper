<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\AdminBar;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;

/**
 * @group feature-admin-bar
 * @group module-admin
 */
class AdminBarTest extends WPTestCase
{
	private Hook $hook;
	private AdminBar $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new AdminBar();
		$this->instance->hook($this->hook);
	}

	/** @testdox should has the callback attached to hook */
	public function testHook(): void
	{
		/**
		 * Test that callbacks should be attached to hooks with the highest priority,
		 * otherwise changes on the Admin Bar might not be properly applied.
		 */
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_bar_menu', [$this->instance, 'removeNodes']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('show_admin_bar', [$this->instance, 'showAdminBar']));
		$this->assertFalse($this->hook->hasAction('admin_bar_menu', [$this->instance, 'addMyAccountNode']));
		$this->assertFalse($this->hook->hasAction('admin_bar_menu', [$this->instance, 'addEnvironmentTypeNode']));

		update_option(Option::name('admin_bar_howdy'), false);
		update_option(Option::name('admin_bar_env_type'), true);

		$this->instance->hook($this->hook);

		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_bar_menu', [$this->instance, 'addMyAccountNode']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_bar_menu', [$this->instance, 'addEnvironmentTypeNode']));
	}
}
