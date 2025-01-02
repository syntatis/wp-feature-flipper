<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Gutenberg;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\General;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;

/**
 * @group feature-gutenberg
 * @group module-general
 */
class GutenbergTest extends WPTestCase
{
	private Hook $hook;
	private Gutenberg $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new Gutenberg();
		$this->instance->hook($this->hook);
	}

	/** @testdox should have callback attached to hooks */
	public function testHook(): void
	{
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('use_block_editor_for_post', [$this->instance, 'filterUseBlockEditorForPost']));
	}

	/** @testdox should default values */
	public function testDefaultOptions(): void
	{
		$this->assertTrue(Option::isOn('gutenberg'));
		$this->assertTrue(Option::isOn('block_based_widgets'));
		$this->assertTrue(Option::isOn('revisions'));
		$this->assertEquals(['post', 'page'], Option::get('gutenberg_post_types'));

		$callback = static fn ($use, $postType) => $postType === 'page';

		add_filter('use_block_editor_for_post_type', $callback, 10, 2);

		$this->assertEquals(['page'], Option::get('gutenberg_post_types'));

		remove_filter('use_block_editor_for_post_type', $callback);
	}

	/** @testdox should return updated values */
	public function testOptionUpdated(): void
	{
		update_option(Option::name('gutenberg'), false);
		update_option(Option::name('gutenberg_post_types'), ['post']);

		$this->assertFalse(Option::isOn('gutenberg'));
		$this->assertEquals(['post'], Option::get('gutenberg_post_types'));
	}

	/** @testdox should return inherited value when post is invalid */
	public function testFilterUseBlockEditorForPostInvalidPost(): void
	{
		$this->assertFalse($this->instance->filterUseBlockEditorForPost(false, 0));
	}

	/** @testdox should return updated value when post is valid */
	public function testFilterUseBlockEditorForPostValidPost(): void
	{
		$postId = self::factory()->post->create();

		$this->assertTrue($this->instance->filterUseBlockEditorForPost(false, $postId));

		$wpPost = self::factory()->post->create_and_get();

		$this->assertTrue($this->instance->filterUseBlockEditorForPost(false, $wpPost));
	}

	/** @testdox should return return `false` when post is not in "gutenberg_post_types" */
	public function testFilterUseBlockEditorForPostUpdated(): void
	{
		update_option(Option::name('gutenberg_post_types'), ['page']);

		$postId = self::factory()->post->create();

		$this->assertFalse($this->instance->filterUseBlockEditorForPost(true, $postId));
	}
}
