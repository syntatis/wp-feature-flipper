<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates;

use SSFV\Codex\Foundation\Hooks\Hook;
use stdClass;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Features\Updates\ManagePlugins;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

/** @group feature-updates */
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

	public function testHookUpdateDisabled(): void
	{
		$this->instance->hook($this->hook);

		$this->assertSame(10, $this->hook->hasAction('admin_init', '_maybe_update_plugins'));
		$this->assertSame(20, $this->hook->hasAction('load-plugins.php', 'wp_plugin_update_rows'));
		$this->assertSame(10, $this->hook->hasAction('load-plugins.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('load-update-core.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('load-update.php', 'wp_update_plugins'));
		$this->assertSame(10, $this->hook->hasAction('wp_update_plugins', 'wp_update_plugins'));

		update_option(Option::name('update_plugins'), false);

		$this->instance->hook($this->hook);

		$this->assertFalse($this->hook->hasAction('admin_init', '_maybe_update_plugins'));
		$this->assertFalse($this->hook->hasAction('load-plugins.php', 'wp_plugin_update_rows'));
		$this->assertFalse($this->hook->hasAction('load-plugins.php', 'wp_update_plugins'));
		$this->assertFalse($this->hook->hasAction('load-update-core.php', 'wp_update_plugins'));
		$this->assertFalse($this->hook->hasAction('load-update.php', 'wp_update_plugins'));
		$this->assertFalse($this->hook->hasAction('wp_update_plugins', 'wp_update_plugins'));

		// Ensure that we do not accidentally disable auto updates.
		$this->assertFalse($this->hook->hasFilter('auto_update_plugin', '__return_false'));
	}

	public function testHookAutoUpdateDisabled(): void
	{
		$this->instance->hook($this->hook);

		$this->assertFalse($this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('auto_update_plugin', '__return_false'));

		update_option(Option::name('auto_update_plugins'), false);

		$this->instance->hook($this->hook);

		$this->assertFalse($this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertSame(10, $this->hook->hasFilter('auto_update_plugin', '__return_false'));
	}

	public function testFilterCoreUpdateTransient(): void
	{
		$cache = new stdClass();
		$cache->response = ['test'];
		$cache->translations = ['test'];

		$cache = $this->instance->filterUpdateTransient($cache);

		$this->assertEmpty($cache->response);
		$this->assertEmpty($cache->translations);
	}
}
