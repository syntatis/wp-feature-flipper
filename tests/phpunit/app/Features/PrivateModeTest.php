<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\SiteAccess;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\PrivateMode;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * @group feature-site-access
 * @group module-site
 */
class PrivateModeTest extends WPTestCase
{
	private PrivateMode $instance;
	private Hook $hook;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new PrivateMode();
		$this->instance->hook($this->hook);
	}

	/** @testdox should has the callback attached to hook */
	public function testHook(): void
	{
		$this->assertFalse($this->hook->hasAction('template_redirect', [$this->instance, 'forceLogin']));
		$this->assertFalse($this->hook->hasAction('rightnow_end', [$this->instance, 'showRightNowStatus']));
		$this->assertFalse($this->hook->hasFilter('login_site_html_link', '__return_empty_string'));

		Option::update('site_access', 'private');
		$this->instance->hook($this->hook);

		$this->assertSame(PHP_INT_MIN, $this->hook->hasAction('template_redirect', [$this->instance, 'forceLogin']));
		$this->assertSame(PHP_INT_MIN, $this->hook->hasAction('rightnow_end', [$this->instance, 'showRightNowStatus']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('login_site_html_link', '__return_empty_string'));
	}

	/** @testdox should carry legacy option */
	public function testOptionLegacy(): void
	{
		$this->assertFalse(Option::isOn('site_private'));
		$this->assertSame('public', Option::get('site_access'));

		// Set the legacy option.
		$this->assertTrue(Option::update('site_private', '1'));
		$this->assertSame('1', Option::get('site_private'));
		$this->assertSame('private', Option::get('site_access'));
	}

	/** @testdox should remove the legacy option and return the added value */
	public function testOptionAdded(): void
	{
		$this->assertFalse(Option::isOn('site_private'));
		$this->assertTrue(Option::update('site_private', '1'));
		$this->assertSame('1', Option::get('site_private'));

		$this->assertTrue(Option::add('site_access', 'maintenance'));
		$this->assertFalse(Option::isOn('site_private'));
		$this->assertSame('maintenance', Option::get('site_access'));
	}

	/** @testdox should remove the legacy option and return the added value */
	public function testOptionUpdated(): void
	{
		$this->assertTrue(Option::add('site_access', 'maintenance'));
		$this->assertSame('maintenance', Option::get('site_access'));

		// Update legacy option.
		$this->assertFalse(Option::isOn('site_private'));
		$this->assertTrue(Option::update('site_private', '1'));
		$this->assertSame('1', Option::get('site_private'));

		// Assert.
		$this->assertTrue(Option::update('site_access', 'public'));
		$this->assertFalse(Option::isOn('site_private'));
		$this->assertSame('public', Option::get('site_access'));
	}
}
