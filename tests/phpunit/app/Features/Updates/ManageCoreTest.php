<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates;

use SSFV\Codex\Foundation\Hooks\Hook;
use stdClass;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Features\Updates\ManageCore;
use Syntatis\FeatureFlipper\Option;
use Syntatis\Tests\WPTestCase;

/** @group feature-updates */
class ManageCoreTest extends WPTestCase
{
	private ManageCore $instance;
	private Hook $hook;

	// phpcs:ignore
	public function set_up(): void
	{
		parent::set_up();

		$this->instance = new ManageCore();
		$this->hook = new Hook();
	}

	public function testHookCoreDisabled(): void
	{
		$this->instance->hook($this->hook);

		$this->assertSame(10, $this->hook->hasAction('admin_init', '_maybe_update_core'));
		$this->assertSame(10, $this->hook->hasAction('wp_maybe_auto_update', 'wp_maybe_auto_update'));
		$this->assertSame(10, $this->hook->hasAction('wp_version_check', 'wp_version_check'));
		$this->assertFalse($this->hook->hasFilter('site_transient_update_core', [$this->instance, 'filterUpdateTransient']));
		$this->assertFalse($this->hook->hasFilter('send_core_update_notification_email', '__return_false'));

		update_option(Option::name('update_core'), false);

		$this->instance->hook($this->hook);

		$this->assertFalse($this->hook->hasAction('admin_init', '_maybe_update_core'));
		$this->assertFalse($this->hook->hasAction('wp_maybe_auto_update', 'wp_maybe_auto_update'));
		$this->assertFalse($this->hook->hasAction('wp_version_check', 'wp_version_check'));
		$this->assertSame(10, $this->hook->hasFilter('site_transient_update_core', [$this->instance, 'filterUpdateTransient']));
		$this->assertSame(10, $this->hook->hasFilter('send_core_update_notification_email', '__return_false'));
	}

	public function testFilterCoreUpdateTransient(): void
	{
		$cache = new stdClass();
		$cache->updates = ['test'];
		$cache->translations = ['test'];

		$cache = $this->instance->filterUpdateTransient($cache);

		$this->assertEmpty($cache->updates);
		$this->assertEmpty($cache->translations);
	}
}
