<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates;

use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Features\Updates\ManageThemes;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-updates
 * @group module-advanced
 */
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

	/** @testdox should have the callback attached to hook */
	public function testHook(): void
	{
		// Themes update related hooks.
		$this->assertSame(10, $this->hook->hasAction('admin_init', '_maybe_update_themes'));
		$this->assertSame(20, $this->hook->hasAction('load-themes.php', 'wp_theme_update_rows'));
		$this->assertSame(10, $this->hook->hasAction('load-themes.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('load-update-core.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('load-update.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('wp_update_themes', 'wp_update_themes'));

		// Themes auto-update related hooks.
		$this->assertFalse($this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('auto_update_plugin', '__return_false'));

		Option::update('update_themes', false);

		$this->instance->hook($this->hook);

		// Themes update related hooks.
		$this->assertFalse($this->hook->hasAction('admin_init', '_maybe_update_themes'));
		$this->assertFalse($this->hook->hasAction('load-themes.php', 'wp_theme_update_rows'));
		$this->assertFalse($this->hook->hasAction('load-themes.php', 'wp_update_themes'));
		$this->assertFalse($this->hook->hasAction('load-update-core.php', 'wp_update_themes'));
		$this->assertFalse($this->hook->hasAction('load-update.php', 'wp_update_themes'));
		$this->assertFalse($this->hook->hasAction('wp_update_themes', 'wp_update_themes'));

		// Themes auto-update related hooks.
		$this->assertSame(10, $this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('auto_update_plugin', '__return_false'));
	}

	/** @testdox should have the callback attached to auto-update related hook */
	public function testHookAutoUpdate(): void
	{
		// Themes update related hooks.
		$this->assertSame(10, $this->hook->hasAction('admin_init', '_maybe_update_themes'));
		$this->assertSame(20, $this->hook->hasAction('load-themes.php', 'wp_theme_update_rows'));
		$this->assertSame(10, $this->hook->hasAction('load-themes.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('load-update-core.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('load-update.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('wp_update_themes', 'wp_update_themes'));

		// Themes auto-update related hooks.
		$this->assertFalse($this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('auto_update_plugin', '__return_false'));

		Option::update('auto_update_themes', false);

		$this->instance->hook($this->hook);

		// Themes update related hooks.
		$this->assertSame(10, $this->hook->hasAction('admin_init', '_maybe_update_themes'));
		$this->assertSame(20, $this->hook->hasAction('load-themes.php', 'wp_theme_update_rows'));
		$this->assertSame(10, $this->hook->hasAction('load-themes.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('load-update-core.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('load-update.php', 'wp_update_themes'));
		$this->assertSame(10, $this->hook->hasAction('wp_update_themes', 'wp_update_themes'));

		// Themes auto-update related hooks.
		$this->assertSame(10, $this->hook->hasFilter('auto_update_theme', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('auto_update_plugin', '__return_false'));
	}

	/** @testdox should return default values */
	public function testOptionsDefault(): void
	{
		$this->assertTrue(Option::isOn('update_themes'));
		$this->assertTrue(Option::isOn('auto_update_themes'));
	}

	/** @testdox should return updated values */
	public function testOptionsUpdated(): void
	{
		Option::update('update_themes', false);
		Option::update('auto_update_themes', false);

		$this->assertFalse(Option::isOn('update_themes'));
		$this->assertFalse(Option::isOn('auto_update_themes'));
	}

	/** @testdox should not affect "update_themes" when "auto_update_themes" is `false` */
	public function testMainOptionWhenAutoUpdateIsFalse(): void
	{
		Option::update('auto_update_themes', false);

		$this->assertFalse(Option::isOn('auto_update_themes'));
		$this->assertTrue(Option::isOn('update_themes'));
	}

	/** @testdox should affect "auto_update_themes" when "update_themes" is `false` */
	public function testAutoUpdateWhenMainOptionIsFalse(): void
	{
		Option::update('update_themes', false);

		$this->assertFalse(Option::isOn('update_themes'));
		$this->assertFalse(Option::isOn('auto_update_themes'));
	}

	/** @testdox should affect all options when "updates" option is `false` */
	public function testOptionsWhenGlobalUpdatesOptionIsFalse(): void
	{
		Option::update('updates', false);

		$this->assertFalse(Option::isOn('update_themes'));
		$this->assertFalse(Option::isOn('auto_update_themes'));
	}

	/** @testdox should affect "auto_update_themes" when "auto_updates" option is `false` */
	public function testOptionsWhenGlobalAutoUpdatesOptionIsFalse(): void
	{
		Option::update('auto_updates', false);

		$this->assertTrue(Option::isOn('update_themes'));
		$this->assertFalse(Option::isOn('auto_update_themes'));
	}

	/** @testdox should prune the themes update transient information */
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
