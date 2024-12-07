<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates;

use SSFV\Codex\Foundation\Hooks\Hook;
use stdClass;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Features\Updates\ManagePlugins;
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

	public function testFilterCoreUpdateTransient(): void
	{
		$cache = new stdClass();
		$cache->resonse = ['test'];
		$cache->translations = ['test'];

		$cache = $this->instance->filterUpdateTransient($cache);

		$this->assertEmpty($cache->response);
		$this->assertEmpty($cache->translations);
	}
}
