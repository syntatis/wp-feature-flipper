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

		register_post_type(
			'product',
			[
				'public' => true,
				'show_in_rest' => true,
				'supports' => ['comments', 'trackbacks', 'editor'],
			],
		);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		unregister_post_type('product');

		parent::tear_down();
	}

	/** @testdox should have callback attached to hooks */
	public function testHook(): void
	{
		$this->assertSame(10, $this->hook->hasAction('syntatis/feature_flipper/updated_options', [$this->instance, 'stashOptions']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('use_block_editor_for_post', [$this->instance, 'filterUseBlockEditorForPost']));
	}

	/** @testdox should default values */
	public function testDefaultOptions(): void
	{
		$this->assertTrue(Option::isOn('gutenberg'));
		$this->assertTrue(Option::isOn('block_based_widgets'));
		$this->assertTrue(Option::isOn('revisions'));
		$this->assertEquals(['post', 'page', 'product'], Option::get('gutenberg_post_types'));

		$callback = static fn ($use, $postType) => $postType === 'page';

		add_filter('use_block_editor_for_post_type', $callback, 10, 2);

		$this->assertEquals(['page'], Option::get('gutenberg_post_types'));

		remove_filter('use_block_editor_for_post_type', $callback);
	}

	/** @testdox should return updated values */
	public function testOptionUpdated(): void
	{
		Option::update('gutenberg', false);
		Option::update('gutenberg_post_types', ['post']);

		$this->assertFalse(Option::isOn('gutenberg'));
		$this->assertTrue(Option::stash('gutenberg_post_types', ['post', 'page', 'product']));
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
		Option::stash('gutenberg_post_types', ['post', 'page', 'product']);
		Option::update('gutenberg_post_types', ['page']);

		$postId = self::factory()->post->create();

		$this->assertFalse($this->instance->filterUseBlockEditorForPost(true, $postId));
	}

	/**
	 * @dataProvider dataStashOptions
	 * @testdox should update the stash option
	 *
	 * @param mixed $expect The expected value returned from the stash option.
	 */
	public function testStashOptions(array $options, $expect): void
	{
		$this->instance->stashOptions($options);

		$this->assertSame($expect, get_option('_' . Option::name('gutenberg_post_types') . '_stash'));
	}

	public static function dataStashOptions(): iterable
	{
		yield [
			[Option::name('gutenberg_post_types')],
			[
				'post',
				'page',
				'product',
			],
		];

		yield [[], false];
		yield [['gutenberg_post_types'], false]; // Invalid option name.
	}
}
