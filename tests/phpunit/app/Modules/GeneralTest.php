<?php

declare(strict_types=1);

namespace Syntatis\Tests\Modules;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\General;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;

/** @group module-general */
class GeneralTest extends WPTestCase
{
	private Hook $hook;
	private General $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new General();
		$this->instance->hook($this->hook);
	}

	public function testHook(): void
	{
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('use_block_editor_for_post', [$this->instance, 'filterUseBlockEditorForPost']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('use_widgets_block_editor', [$this->instance, 'filterUseWidgetsBlockEditor']));
	}

	/** @testdox should default values */
	public function testDefaultOptions(): void
	{
		$this->assertTrue(Option::get('gutenberg'));
		$this->assertTrue(Option::get('block_based_widgets'));
		$this->assertEquals(['post', 'page'], Option::get('gutenberg_post_types'));
	}

	/** @testdox should return updated values */
	public function testOptionUpdated(): void
	{
		update_option(Option::name('gutenberg'), false);
		update_option(Option::name('gutenberg_post_types'), ['post']);
		update_option(Option::name('block_based_widgets'), false);

		$this->assertFalse(Option::get('gutenberg'));
		$this->assertEquals(['post'], Option::get('gutenberg_post_types'));
		$this->assertFalse(Option::get('block_based_widgets'));
	}

	/** @testdox should return inherited value */
	public function testFilterUseWidgetsBlockEditor(): void
	{
		$this->assertTrue($this->instance->filterUseWidgetsBlockEditor(true));
	}

	/** @testdox should return updated value */
	public function testFilterUseWidgetsBlockEditorUpdated(): void
	{
		update_option(Option::name('block_based_widgets'), false);

		$this->assertFalse($this->instance->filterUseWidgetsBlockEditor(true));

		update_option(Option::name('block_based_widgets'), true);

		$this->assertTrue($this->instance->filterUseWidgetsBlockEditor(false));
	}
}