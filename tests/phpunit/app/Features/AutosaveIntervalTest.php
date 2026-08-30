<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use ReflectionProperty;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\AutosaveInterval;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\InlineData;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-autosave-interval
 * @group module-general
 */
class AutosaveIntervalTest extends WPTestCase
{
	private AutosaveInterval $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->instance = new AutosaveInterval();

		// Reset the state captured during the plugin bootstrap.
		$reflection = new ReflectionProperty(AutosaveInterval::class, 'wasPredefined');
		$reflection->setAccessible(true);
		$reflection->setValue(null, false);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		unset($_GET['tab']);

		parent::tear_down();
	}

	/** @testdox should default to the WordPress interval of 60 seconds */
	public function testDefaultInterval(): void
	{
		$this->assertSame(60, AutosaveInterval::effectiveInterval());
		$this->assertFalse(AutosaveInterval::hasExplicitConfig());
	}

	/** @testdox should apply a custom interval */
	public function testCustomInterval(): void
	{
		Option::update('autosave_interval_enabled', true);
		Option::update('autosave_interval', 30);

		$this->assertSame(30, AutosaveInterval::effectiveInterval());
	}

	/** @testdox should fall back to the default when the custom interval is disabled */
	public function testCustomIntervalDisabled(): void
	{
		Option::update('autosave_interval_enabled', false);
		Option::update('autosave_interval', 30);

		$this->assertSame(60, AutosaveInterval::effectiveInterval());
	}

	/** @testdox should add the Autosave Interval values to the inline data */
	public function testFilterInlineData(): void
	{
		$_GET['tab'] = 'admin';

		$data = $this->instance->filterInlineData(new InlineData());

		$this->assertFalse(isset($data['features']['autosaveInterval']));

		$_GET['tab'] = 'general';

		$data = $this->instance->filterInlineData(new InlineData());

		$this->assertSame(
			[
				'isLocked' => false,
				'defaultInterval' => 60,
				'minInterval' => 15,
				'maxInterval' => 3600,
			],
			$data['features']['autosaveInterval'],
		);
	}

	/** @testdox should respect a pre-defined AUTOSAVE_INTERVAL constant */
	public function testHookRespectsPredefinedConstant(): void
	{
		Option::update('autosave_interval_enabled', true);
		Option::update('autosave_interval', 30);

		$hook = new Hook();
		$this->instance->hook($hook);

		$this->assertSame(60, AUTOSAVE_INTERVAL);
		$this->assertTrue(AutosaveInterval::hasExplicitConfig());
		$this->assertSame(60, AutosaveInterval::effectiveInterval());
		$this->assertSame(10, $hook->hasFilter(Option::hook('sanitize:autosave_interval'), [$this->instance, 'sanitize']));
		$this->assertSame(10, $hook->hasFilter('syntatis/feature_flipper/inline_data', [$this->instance, 'filterInlineData']));
	}

	/**
	 * @dataProvider dataInvalidValues
	 * @testdox should normalize invalid values to the default
	 *
	 * @param mixed $value The invalid value.
	 */
	public function testSanitizeInvalidValues($value): void
	{
		$this->assertSame(60, $this->instance->sanitize($value));
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
		yield 'zero' => [0];
		yield 'decimal' => ['7.5'];
		yield 'below minimum' => [14];
		yield 'too large' => [999999];
		yield 'null' => [null];
	}

	public function dataValidValues(): iterable
	{
		yield 'integer' => [30, 30];
		yield 'numeric string' => ['120', 120];
		yield 'minimum' => [15, 15];
		yield 'maximum' => [3600, 3600];
	}
}
