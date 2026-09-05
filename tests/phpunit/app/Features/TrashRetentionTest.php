<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use ReflectionProperty;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Contracts\InlineDataProvider;
use Syntatis\FeatureFlipper\Features\TrashRetention;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\InlineData;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-trash-retention
 * @group module-general
 */
class TrashRetentionTest extends WPTestCase
{
	private TrashRetention $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->instance = new TrashRetention();

		// Reset the state captured during the plugin bootstrap.
		$reflection = new ReflectionProperty(TrashRetention::class, 'wasPredefined');
		$reflection->setAccessible(true);
		$reflection->setValue(null, false);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		unset($_GET['tab']);

		parent::tear_down();
	}

	/** @testdox should return the WordPress default of 30 days */
	public function testDefaultRetentionDays(): void
	{
		$this->assertSame(30, TrashRetention::retentionDays());
		$this->assertFalse(TrashRetention::hasExplicitConfig());
	}

	/** @testdox should return a custom retention period */
	public function testCustomRetentionDays(): void
	{
		Option::update('trash_retention', 7);

		$this->assertSame(7, TrashRetention::retentionDays());
	}

	/** @testdox should return 0 when the Trash is disabled */
	public function testDisableTrashRetentionDays(): void
	{
		Option::update('trash_retention', 0);

		$this->assertSame(0, TrashRetention::retentionDays());
	}

	/** @testdox should return the effective retention period */
	public function testEffectiveDays(): void
	{
		$this->assertSame(30, TrashRetention::effectiveDays());

		Option::update('trash_retention', 14);

		$this->assertSame(14, TrashRetention::effectiveDays());
	}

	/** @testdox should add the Trash retention values to the inline data */
	public function testInlineData(): void
	{
		$_GET['tab'] = 'admin';

		$data = $this->instance->inlineData(new InlineData());

		$this->assertFalse(isset($data['features']['trashRetention']));

		$_GET['tab'] = 'general';

		$data = $this->instance->inlineData(new InlineData());

		$this->assertSame(
			[
				'isLocked' => false,
				'days' => 30,
				'defaultDays' => 30,
				'maxDays' => 3650,
			],
			$data['features']['trashRetention'],
		);
	}

	/** @testdox should respect a pre-defined EMPTY_TRASH_DAYS constant */
	public function testHookRespectsPredefinedConstant(): void
	{
		Option::update('trash_retention', 7);

		$hook = new Hook();
		$this->instance->hook($hook);

		$this->assertSame(30, EMPTY_TRASH_DAYS);
		$this->assertTrue(TrashRetention::hasExplicitConfig());
		$this->assertSame(30, TrashRetention::effectiveDays());
		$this->assertSame(10, $hook->hasFilter(Option::hook('sanitize:trash_retention'), [$this->instance, 'sanitize']));
		$this->assertInstanceOf(InlineDataProvider::class, $this->instance);
	}

	/**
	 * @dataProvider dataInvalidValues
	 * @testdox should normalize invalid values to the default
	 *
	 * @param mixed $value The invalid value.
	 */
	public function testSanitizeInvalidValues($value): void
	{
		$this->assertSame(30, $this->instance->sanitize($value));
	}

	/**
	 * @dataProvider dataValidValues
	 * @testdox should normalize valid values
	 *
	 * @param mixed $value The valid value.
	 */
	public function testSanitizeValidValues($value, int $expected): void
	{
		$this->assertSame($expected, $this->instance->sanitize($value));
	}

	public function dataInvalidValues(): iterable
	{
		yield 'empty string' => [''];
		yield 'non-numeric' => ['abc'];
		yield 'negative number' => [-5];
		yield 'decimal' => ['7.5'];
		yield 'too large' => [999999];
		yield 'null' => [null];
	}

	public function dataValidValues(): iterable
	{
		yield 'zero' => [0, 0];
		yield 'integer' => [7, 7];
		yield 'numeric string' => ['14', 14];
		yield 'maximum' => [3650, 3650];
	}
}
