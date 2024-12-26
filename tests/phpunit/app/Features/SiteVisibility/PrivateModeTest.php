<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\SiteVisibility;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\SiteVisibility\PrivateMode;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * @group feature-site-visibility
 * @group module-site
 */
class PrivateModeTest extends WPTestCase
{
	private PrivateMode $instance;
	private Hook $hook;

	// phpcs:ignore
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

	/** @testdox should carry lagacy option */
	public function testOptionLegacy(): void
	{
	}
}
