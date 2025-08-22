<?php

declare(strict_types=1);

namespace Syntatis\Tests\Modules;

use Faker\Factory;
use Faker\Generator;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\General;
use Syntatis\Tests\WPTestCase;

/** @group module-general */
class GeneralTest extends WPTestCase
{
	private Hook $hook;
	private General $instance;
	private static Generator $faker;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new General();
		$this->instance->hook($this->hook);
		$this->faker = Factory::create();
	}

	/** @testdox should default values */
	public function testDefaultOptions(): void
	{
		$this->assertTrue(Option::isOn('block_based_widgets'));
		$this->assertTrue(Option::isOn('revisions'));
		$this->assertFalse(Option::isOn('revisions_max_enabled'));
		$this->assertSame(5, Option::get('revisions_max'));
	}

	/** @testdox should return updated values */
	public function testOptionUpdated(): void
	{
		Option::update('block_based_widgets', false);
		Option::update('revisions', false);
		Option::update('revisions_max_enabled', true);
		Option::update('revisions_max', 10);

		$this->assertFalse(Option::isOn('block_based_widgets'));
		$this->assertFalse(Option::isOn('revisions'));
		$this->assertTrue(Option::isOn('revisions_max_enabled'));
		$this->assertSame(10, Option::get('revisions_max'));
	}

	/** @testdox should return inherited value */
	public function testFilterUseWidgetsBlockEditor(): void
	{
		$this->assertTrue(wp_use_widgets_block_editor());
	}

	/** @testdox should return updated value */
	public function testFilterUseWidgetsBlockEditorUpdated(): void
	{
		Option::update('block_based_widgets', false);

		$this->assertFalse(wp_use_widgets_block_editor());

		Option::update('block_based_widgets', true);

		$this->assertTrue(wp_use_widgets_block_editor());
	}

	/**
	 * @group feature-revisions
	 * @testdox should be enabled or disabled based on the "revisions" option value
	 */
	public function testRevisionsEnabled(): void
	{
		$postId = self::factory()->post->create(['post_type' => 'post']);

		$this->assertTrue(Option::isOn('revisions'));
		$this->assertTrue(wp_revisions_enabled(get_post($postId)));
		$this->assertSame(-1, wp_revisions_to_keep(get_post($postId)));

		Option::update('revisions', false);

		$this->assertFalse(Option::isOn('revisions'));
		$this->assertFalse(wp_revisions_enabled(get_post($postId)));
		$this->assertSame(0, wp_revisions_to_keep(get_post($postId)));
	}

	/**
	 * @group feature-revisions
	 * @testdox should return the number of revisions to keep
	 */
	public function testRevisionsMaxNumber(): void
	{
		$postId = self::factory()->post->create(['post_type' => 'post']);

		$this->assertFalse(Option::isOn('revisions_max_enabled'));
		$this->assertSame(-1, wp_revisions_to_keep(get_post($postId)));

		Option::update('revisions_max_enabled', true);

		$this->assertSame(5, wp_revisions_to_keep(get_post($postId)));

		Option::update('revisions_max', 10);

		$this->assertSame(10, wp_revisions_to_keep(get_post($postId)));
	}
}
