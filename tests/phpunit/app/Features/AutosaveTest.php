<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Autosave;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;
use WP_Scripts;

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
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		parent::tear_down();

		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_scripts'] = $this->wpScripts;
	}

	/** @testdox should have the callbacks attached to hooks */
	public function testHook(): void
	{
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('init', [$this->instance, 'deregisterScripts']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('block_editor_settings_all', [$this->instance, 'filterBlockEditorSettings']));
	}

	/** @testdox should deregister the "autosave" script when the feature is off */
	public function testDeregisterScripts(): void
	{
		$this->instance->deregisterScripts();

		$this->assertTrue(wp_script_is('autosave', 'registered'));

		Option::update('autosave', false);

		$this->instance->deregisterScripts();

		$this->assertFalse(wp_script_is('autosave', 'registered'));
	}

	/** @testdox should disable the block editor autosave when the feature is off */
	public function testFilterBlockEditorSettings(): void
	{
		$this->assertSame(
			['someSetting' => 'value'],
			$this->instance->filterBlockEditorSettings(['someSetting' => 'value']),
		);

		Option::update('autosave', false);

		$this->assertSame(
			['someSetting' => 'value', 'autosaveInterval' => 0],
			$this->instance->filterBlockEditorSettings(['someSetting' => 'value']),
		);
	}
}
