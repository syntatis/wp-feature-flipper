<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Updates;

use SSFV\Codex\Foundation\Hooks\Hook;
use stdClass;
use Syntatis\FeatureFlipper\Features\Updates;
use Syntatis\FeatureFlipper\Features\Updates\ManageCore;
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
