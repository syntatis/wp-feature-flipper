<?php

declare(strict_types=1);

namespace Syntatis\Tests;

use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Upgrade;

use function add_option;
use function delete_option;
use function get_option;
use function update_option;

/** @group upgrade */
class UpgradeTest extends WPTestCase
{
	private Hook $hook;
	private Upgrade $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new Upgrade();
		$this->instance->hook($this->hook);
	}

	/** @testdox should run the migration on the "plugins_loaded" hook */
	public function testHook(): void
	{
		$this->assertSame(
			10,
			$this->hook->hasAction('plugins_loaded', [$this->instance, 'run']),
		);
	}

	/** @testdox should keep unlimited revisions when the cap was disabled */
	public function testMigrateDisabledMax(): void
	{
		// "add_option" is used because "update_option" with a "false" value is
		// a no-op for a non-existent option, which wouldn't create the legacy
		// option the migration is meant to clean up.
		add_option(Option::name('revisions_max_enabled'), false);
		update_option(Option::name('revisions_max'), 5);

		$this->instance->run();

		$this->assertSame(-1, Option::get('revisions_max'));
		$this->assertFalse(get_option(Option::name('revisions_max_enabled')));
	}

	/** @testdox should keep the configured cap when it was enabled */
	public function testMigrateEnabledMax(): void
	{
		update_option(Option::name('revisions_max_enabled'), true);
		update_option(Option::name('revisions_max'), 10);

		$this->instance->run();

		$this->assertSame(10, Option::get('revisions_max'));
		$this->assertFalse(get_option(Option::name('revisions_max_enabled')));
	}

	/** @testdox should store the previous default cap when it was enabled without a value */
	public function testMigrateEnabledMaxWithoutValue(): void
	{
		update_option(Option::name('revisions_max_enabled'), true);
		delete_option(Option::name('revisions_max'));

		$this->instance->run();

		$this->assertSame(5, Option::get('revisions_max'));
		$this->assertFalse(get_option(Option::name('revisions_max_enabled')));
	}

	/** @testdox should do nothing when there is no legacy option */
	public function testNoLegacyOption(): void
	{
		delete_option(Option::name('revisions_max_enabled'));
		delete_option(Option::name('revisions_max'));

		$this->instance->run();

		$this->assertFalse(get_option(Option::name('revisions_max_enabled')));
		$this->assertSame(-1, Option::get('revisions_max'));
	}
}
