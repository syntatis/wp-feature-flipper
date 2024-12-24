<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Features\Updates\ManageCore;
use Syntatis\FeatureFlipper\Helpers\Option;
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

		$this->hook = new Hook();
		$this->instance = new ManageCore();
		$this->instance->hook($this->hook);
	}

	/** @testdox should has the callback attached to hook */
	public function testHook(): void
	{
		$this->assertSame(10, $this->hook->hasAction('admin_init', '_maybe_update_core'));
		$this->assertSame(10, $this->hook->hasAction('wp_maybe_auto_update', 'wp_maybe_auto_update'));
		$this->assertSame(10, $this->hook->hasAction('wp_version_check', 'wp_version_check'));
		$this->assertFalse($this->hook->hasFilter('site_transient_update_core', [$this->instance, 'filterSiteTransientUpdate']));
		$this->assertFalse($this->hook->hasFilter('send_core_update_notification_email', '__return_false'));

		update_option(Option::name('update_core'), false);

		$this->instance->hook($this->hook);

		$this->assertFalse($this->hook->hasAction('admin_init', '_maybe_update_core'));
		$this->assertFalse($this->hook->hasAction('wp_maybe_auto_update', 'wp_maybe_auto_update'));
		$this->assertFalse($this->hook->hasAction('wp_version_check', 'wp_version_check'));
		$this->assertSame(10, $this->hook->hasFilter('site_transient_update_core', [$this->instance, 'filterSiteTransientUpdate']));
		$this->assertSame(10, $this->hook->hasFilter('send_core_update_notification_email', '__return_false'));
	}

	/** @testdox should return default values */
	public function testOptionsDefault(): void
	{
		$this->assertTrue(Option::get('update_core'));
		$this->assertTrue(Option::get('auto_update_core'));
	}

	/** @testdox should return updated values */
	public function testOptionsUpdated(): void
	{
		update_option(Option::name('update_core'), false);
		update_option(Option::name('auto_update_core'), false);

		$this->assertFalse(Option::get('update_core'));
		$this->assertFalse(Option::get('auto_update_core'));
	}

	/** @testdox should not affect "update_core" when "auto_update_core" is `false` */
	public function testMainOptionWhenAutoUpdateIsFalse(): void
	{
		update_option(Option::name('auto_update_core'), false);

		$this->assertFalse(Option::get('auto_update_core'));
		$this->assertTrue(Option::get('update_core'));
	}

	/** @testdox should affect affect "auto_update_core" when "update_core" is `false` */
	public function testAutoUpdateWhenMainOptionIsFalse(): void
	{
		update_option(Option::name('update_core'), false);

		$this->assertFalse(Option::get('update_core'));
		$this->assertFalse(Option::get('auto_update_core'));
	}

	/** @testdox should affect all options when "updates" option is `false` */
	public function testOptionsWhenGlobalUpdatesOptionIsFalse(): void
	{
		update_option(Option::name('updates'), false);

		$this->assertFalse(Option::get('update_core'));
		$this->assertFalse(Option::get('auto_update_core'));
	}

	/** @testdox should affect "auto_update_core" when "auto_updates" option is `false` */
	public function testOptionsWhenGlobalAutoUpdatesOptionIsFalse(): void
	{
		update_option(Option::name('auto_updates'), false);

		$this->assertTrue(Option::get('update_core'));
		$this->assertFalse(Option::get('auto_update_core'));
	}

	/** @testdox should prune the core update transient information */
	public function testFilterCoreUpdateTransient(): void
	{
		$cache = $this->instance->filterSiteTransientUpdate(
			(object) [
				'translations' => ['test'],
				'updates' => ['test'],
				'version_checked' => '6.8-rc.10000',
			],
		);

		$this->assertEmpty($cache->translations);
		$this->assertEmpty($cache->updates);
		$this->assertIsArray($cache->translations);
		$this->assertIsArray($cache->updates);

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress convention.
		$this->assertSame($GLOBALS['wp_version'], $cache->version_checked);
	}
}
