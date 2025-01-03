<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Features\Updates\ManagePlugins;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-updates
 * @group module-advanced
 */
class ManagePluginsTest extends WPTestCase
{
	private ManagePlugins $instance;
	private Hook $hook;

	// phpcs:ignore
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new ManagePlugins();
		$this->instance->hook($this->hook);
	}

	/** @testdox should has the callback attached to hook */
	public function testHook(): void
	{
		// Plugin updates related hooks.
		$this->assertSame(10, $this->hook->hasAction('admin_init', '_maybe_update_plugins'));
		$this->assertSame(20, $this->hook->hasAction('load-plugins.php', 'wp_plugin_update_rows'));
		$this->assertSame(10, $this->hook->hasAction('load-plugins.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('load-update-core.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('load-update.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('wp_update_plugins', 'wp_update_plugins'));

		// Plugin auto-updates related hooks.
		$this->assertFalse($this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('auto_update_plugin', '__return_false'));

		Option::update('update_plugins', false);

		$this->instance->hook($this->hook);

		// Plugin updates related hooks.
		$this->assertFalse($this->hook->hasAction('admin_init', '_maybe_update_plugins'));
		$this->assertFalse($this->hook->hasAction('load-plugins.php', 'wp_plugin_update_rows'));
		$this->assertFalse($this->hook->hasAction('load-plugins.php', 'wp_update_plugins'));
		$this->assertFalse($this->hook->hasAction('load-update-core.php', 'wp_update_plugins'));
		$this->assertFalse($this->hook->hasAction('load-update.php', 'wp_update_plugins'));
		$this->assertFalse($this->hook->hasAction('wp_update_plugins', 'wp_update_plugins'));

		// Plugin auto-updates related hooks.
		$this->assertFalse($this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertSame(10, $this->hook->hasFilter('auto_update_plugin', '__return_false'));
	}

	/** @testdox should has the callback attached to auto-update related hook */
	public function testHookAutoUpdate(): void
	{
		// Plugin updates related hooks.
		$this->assertSame(10, $this->hook->hasAction('admin_init', '_maybe_update_plugins'));
		$this->assertSame(20, $this->hook->hasAction('load-plugins.php', 'wp_plugin_update_rows'));
		$this->assertSame(10, $this->hook->hasAction('load-plugins.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('load-update-core.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('load-update.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('wp_update_plugins', 'wp_update_plugins'));

		// Plugin auto-updates related hooks.
		$this->assertFalse($this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('auto_update_plugin', '__return_false'));

		Option::update('auto_update_plugins', false);

		$this->instance->hook($this->hook);

		// Plugin updates related hooks.
		$this->assertSame(10, $this->hook->hasAction('admin_init', '_maybe_update_plugins'));
		$this->assertSame(20, $this->hook->hasAction('load-plugins.php', 'wp_plugin_update_rows'));
		$this->assertSame(10, $this->hook->hasAction('load-plugins.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('load-update-core.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('load-update.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('wp_update_plugins', 'wp_update_plugins'));

		// Plugin auto-updates related hooks.
		$this->assertFalse($this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertSame(10, $this->hook->hasFilter('auto_update_plugin', '__return_false'));
	}

	/** @testdox should return default values */
	public function testOptionsDefault(): void
	{
		$this->assertTrue(Option::isOn('update_plugins'));
		$this->assertTrue(Option::isOn('auto_update_plugins'));
	}

	/** @testdox should return updated values */
	public function testOptionsUpdated(): void
	{
		Option::update('update_plugins', false);
		Option::update('auto_update_plugins', false);

		$this->assertFalse(Option::isOn('update_plugins'));
		$this->assertFalse(Option::isOn('auto_update_plugins'));
	}

	/** @testdox should not affect "update_plugins" when "auto_update_plugins" is `false` */
	public function testMainOptionWhenAutoUpdateIsFalse(): void
	{
		Option::update('auto_update_plugins', false);

		$this->assertFalse(Option::isOn('auto_update_plugins'));
		$this->assertTrue(Option::isOn('update_plugins'));
	}

	/** @testdox should affect "auto_update_plugins" when "update_plugins" is `false` */
	public function testAutoUpdateWhenMainOptionIsFalse(): void
	{
		Option::update('update_plugins', false);

		$this->assertFalse(Option::isOn('update_plugins'));
		$this->assertFalse(Option::isOn('auto_update_plugins'));
	}

	/** @testdox should affect all options when "updates" option is `false` */
	public function testOptionsWhenGlobalUpdatesOptionIsFalse(): void
	{
		Option::update('updates', false);

		$this->assertFalse(Option::isOn('update_plugins'));
		$this->assertFalse(Option::isOn('auto_update_plugins'));
	}

	/** @testdox should affect "auto_update_plugins" when "auto_updates" option is `false` */
	public function testOptionsWhenGlobalAutoUpdatesOptionIsFalse(): void
	{
		Option::update('auto_updates', false);

		$this->assertTrue(Option::isOn('update_plugins'));
		$this->assertFalse(Option::isOn('auto_update_plugins'));
	}

	/** @testdox should prune the plugins update transient information */
	public function testFilterSiteTransientUpdate(): void
	{
		$cache = $this->instance->filterSiteTransientUpdate((object) [
			'translations' => ['test'],
			'response' => ['test'],
		]);

		$this->assertEmpty($cache->response);
		$this->assertEmpty($cache->translations);
	}
}
