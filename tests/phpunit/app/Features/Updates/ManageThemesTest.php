<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates;

use SSFV\Codex\Foundation\Hooks\Hook;
use stdClass;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Features\Updates\ManageThemes;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

/** @group feature-updates */
class ManageThemesTest extends WPTestCase
{
	private ManageThemes $instance;
	private Hook $hook;

	// phpcs:ignore
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new ManageThemes();
		$this->instance->hook($this->hook);
	}

	public function testHookUpdateDisabled(): void
	{
		$this->instance->hook($this->hook);

		$this->assertSame(10, $this->hook->hasAction('admin_init', '_maybe_update_themes'));
		$this->assertSame(20, $this->hook->hasAction('load-themes.php', 'wp_theme_update_rows'));
		$this->assertSame(10, $this->hook->hasAction('load-themes.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('load-update-core.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('load-update.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('wp_update_themes', 'wp_update_themes'));

		update_option(Option::name('update_themes'), false);

		$this->instance->hook($this->hook);

		$this->assertFalse($this->hook->hasAction('admin_init', '_maybe_update_themes'));
		$this->assertFalse($this->hook->hasAction('load-themes.php', 'wp_theme_update_rows'));
		$this->assertFalse($this->hook->hasAction('load-themes.php', 'wp_update_themes'));
		$this->assertFalse($this->hook->hasAction('load-update-core.php', 'wp_update_themes'));
		$this->assertFalse($this->hook->hasAction('load-update.php', 'wp_update_themes'));
		$this->assertFalse($this->hook->hasAction('wp_update_themes', 'wp_update_themes'));

		// Ensure that we do not accidentally disable auto updates.
		$this->assertFalse($this->hook->hasFilter('auto_update_theme', '__return_false'));
	}

	public function testHookAutoUpdateDisabled(): void
	{
		$this->instance->hook($this->hook);

		$this->assertFalse($this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('auto_update_plugin', '__return_false'));

		update_option(Option::name('auto_update_themes'), false);

		$this->instance->hook($this->hook);

		$this->assertSame(10, $this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('auto_update_plugin', '__return_false'));
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
