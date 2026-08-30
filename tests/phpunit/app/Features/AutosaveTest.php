<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use ReflectionProperty;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Autosave;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\InlineData;
use Syntatis\Tests\WPTestCase;
use WP_Scripts;

use function implode;
use function is_object;

use const PHP_INT_MAX;

/**
 * @group feature-autosave
 * @group module-general
 */
class AutosaveTest extends WPTestCase
{
	/**
	 * Stores the original `WP_Scripts` instance.
	 */
	private ?WP_Scripts $wpScripts;
	private Hook $hook;
	private Autosave $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$wpScripts = $GLOBALS['wp_scripts'] ?? null;
		$this->wpScripts = is_object($wpScripts) ? clone $wpScripts : null;

		$this->hook = new Hook();
		$this->instance = new Autosave();
		$this->instance->hook($this->hook);

		// Reset the state captured during the plugin bootstrap and hook registration.
		$reflection = new ReflectionProperty(Autosave::class, 'wasPredefined');
		$reflection->setAccessible(true);
		$reflection->setValue(null, false);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		unset($_GET['tab']);

		parent::tear_down();

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_scripts'] = $this->wpScripts;
	}

	/** @testdox should have the callbacks attached to hooks */
	public function testHook(): void
	{
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('init', [$this->instance, 'deregisterScripts']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('block_editor_settings_all', [$this->instance, 'filterBlockEditorSettings']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('enqueue_block_editor_assets', [$this->instance, 'clearLocalAutosaveStorage']));
		$this->assertSame(10, $this->hook->hasFilter(Option::hook('sanitize:autosave_interval'), [$this->instance, 'sanitize']));
		$this->assertSame(10, $this->hook->hasFilter('syntatis/feature_flipper/inline_data', [$this->instance, 'filterInlineData']));
	}

	/** @testdox should default to the WordPress interval of 60 seconds */
	public function testDefaultInterval(): void
	{
		$this->assertSame(60, Autosave::effectiveInterval());
		$this->assertFalse(Autosave::hasExplicitConfig());
	}

	/** @testdox should apply a custom interval */
	public function testCustomInterval(): void
	{
		Option::update('autosave_interval', 30);

		$this->assertSame(30, Autosave::effectiveInterval());
	}

	/** @testdox should keep the "autosave" script registered when the feature is on */
	public function testScriptRegisteredWhenEnabled(): void
	{
		$this->instance->deregisterScripts();

		$this->assertTrue(wp_script_is('autosave', 'registered'));
	}

	/** @testdox should deregister the "autosave" script when the feature is off */
	public function testDeregisterScriptsWhenDisabled(): void
	{
		Option::update('autosave', false);

		$this->instance->deregisterScripts();

		$this->assertFalse(wp_script_is('autosave', 'registered'));
	}

	/** @testdox should keep the block editor settings unchanged when the feature is on */
	public function testBlockEditorSettingsWhenEnabled(): void
	{
		$this->assertSame(
			['someSetting' => 'value'],
			$this->instance->filterBlockEditorSettings(['someSetting' => 'value']),
		);
	}

	/** @testdox should disable the block editor autosave when the feature is off */
	public function testBlockEditorSettingsWhenDisabled(): void
	{
		Option::update('autosave', false);

		$this->assertSame(
			[
				'someSetting' => 'value',
				'autosaveInterval' => 0,
				'localAutosaveInterval' => 0,
			],
			$this->instance->filterBlockEditorSettings(['someSetting' => 'value']),
		);
	}

	/** @testdox should clear the local autosave backups when the feature is off */
	public function testClearLocalAutosaveStorage(): void
	{
		$this->instance->clearLocalAutosaveStorage();

		$onData = implode('', (array) wp_scripts()->get_data('wp-edit-post', 'after'));
		$this->assertStringNotContainsString('wp-autosave-', $onData);

		Option::update('autosave', false);

		$this->instance->clearLocalAutosaveStorage();

		$offData = implode('', (array) wp_scripts()->get_data('wp-edit-post', 'after'));
		$this->assertStringContainsString('wp-autosave-', $offData);
	}

	/** @testdox should add the Autosave values to the inline data */
	public function testFilterInlineData(): void
	{
		$_GET['tab'] = 'admin';

		$data = $this->instance->filterInlineData(new InlineData());

		$this->assertFalse(isset($data['features']['autosave']));

		$_GET['tab'] = 'general';

		$data = $this->instance->filterInlineData(new InlineData());

		$this->assertSame(
			[
				'isLocked' => false,
				'interval' => 60,
				'defaultInterval' => 60,
				'minInterval' => 5,
				'maxInterval' => 3600,
			],
			$data['features']['autosave'],
		);
	}

	/** @testdox should respect a pre-defined AUTOSAVE_INTERVAL constant */
	public function testHookRespectsPredefinedConstant(): void
	{
		Option::update('autosave_interval', 30);

		$hook = new Hook();
		$this->instance->hook($hook);

		$this->assertSame(60, AUTOSAVE_INTERVAL);
		$this->assertTrue(Autosave::hasExplicitConfig());
		$this->assertSame(60, Autosave::effectiveInterval());
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
		yield 'below minimum' => [4];
		yield 'too large' => [999999];
		yield 'null' => [null];
	}

	public function dataValidValues(): iterable
	{
		yield 'integer' => [30, 30];
		yield 'numeric string' => ['120', 120];
		yield 'minimum' => [5, 5];
		yield 'maximum' => [3600, 3600];
	}
}
