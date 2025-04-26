<?php

declare(strict_types=1);

namespace Syntatis\Tests\Modules;

use Faker\Factory;
use Faker\Generator;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\General;
use Syntatis\Tests\WPTestCase;
use WPDieException;

use const PHP_INT_MAX;

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

	/** @testdox should have callback attached to hooks */
	public function testHook(): void
	{
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('use_widgets_block_editor', [$this->instance, 'filterUseWidgetsBlockEditor']));
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
		$this->assertTrue($this->instance->filterUseWidgetsBlockEditor(true));
	}

	/** @testdox should return updated value */
	public function testFilterUseWidgetsBlockEditorUpdated(): void
	{
		Option::update('block_based_widgets', false);

		$this->assertFalse($this->instance->filterUseWidgetsBlockEditor(true));

		Option::update('block_based_widgets', true);

		$this->assertTrue($this->instance->filterUseWidgetsBlockEditor(false));
	}

	/** @testdox should return the comment value as is */
	public function testFilterPreprocessComment(): void
	{
		$comment = $this->instance->filterPreprocessComment(['comment_content' => 'hello world!']);

		$this->assertEquals(['comment_content' => 'hello world!'], $comment);
	}

	/** @testdox should fail with WPDieException exception */
	public function testFilterPreprocessCommentFailed(): void
	{
		$this->expectException(WPDieException::class);
		$this->expectExceptionMessage('Comment&#039;s too short. Please write something more helpful.');

		$this->instance->filterPreprocessComment(['comment_content' => 'hi']);
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

	/**
	 * @group feature-comments
	 * @testdox should return the comment value or fail the comment is too short (default: 10 characters)
	 */
	public function testMinCommentLengthDefault(): void
	{
		$comment = $this->instance->filterPreprocessComment(['comment_content' => 'hello world!']); // 12 characters.

		$this->assertEquals(['comment_content' => 'hello world!'], $comment);

		$this->expectException(WPDieException::class);
		$this->expectExceptionMessage('Comment&#039;s too short. Please write something more helpful.');

		$this->instance->filterPreprocessComment(['comment_content' => 'Hi!']); // 3 characters.
	}

	/**
	 * @group feature-comments
	 * @testdox should return the comment value or fail the comment is too short (updated: 20 characters)
	 */
	public function testMinCommentLengthUpdated(): void
	{
		Option::update('comment_min_length', 20);

		$this->assertSame(20, Option::get('comment_min_length'));

		// Asserting with value about 20 characters.
		$text = $this->faker->realTextBetween(20, 30);
		$this->assertEquals(
			['comment_content' => $text],
			$this->instance->filterPreprocessComment(
				['comment_content' => $text],
			),
		);

		$this->expectException(WPDieException::class);
		$this->expectExceptionMessage('Comment&#039;s too short. Please write something more helpful.');

		$comment = $this->instance->filterPreprocessComment(['comment_content' => $this->faker->text(19)]);
	}

	/**
	 * @group feature-comments
	 * @testdox should return the comment value or fail when the comment is too long (default: 100 characters)
	 */
	public function testMaxCommentLengthDefault(): void
	{
		$this->assertSame(100, Option::get('comment_max_length'));

		// Asserting with value about 100 characters.
		$text = $this->faker->text(100);
		$this->assertEquals(
			['comment_content' => $text],
			$this->instance->filterPreprocessComment(
				['comment_content' => $text],
			),
		);

		$this->expectException(WPDieException::class);
		$this->expectExceptionMessage('Comment&#039;s too long. Please write more concisely.');

		// Asserting with value about 101 or more characters.
		$this->instance->filterPreprocessComment(['comment_content' => $this->faker->realTextBetween(101, 200)]);
	}

	/**
	 * @group feature-comments
	 * @testdox should return the comment value or fail when the comment is too long (300 characters)
	 */
	public function testMaxCommentLengthUpdated(): void
	{
		Option::update('comment_max_length', 300);

		$this->assertSame(300, Option::get('comment_max_length'));

		// Asserting with value about 300 characters.
		$text = $this->faker->text(300);
		$this->assertEquals(
			['comment_content' => $text],
			$this->instance->filterPreprocessComment(
				['comment_content' => $text],
			),
		);

		$this->expectException(WPDieException::class);
		$this->expectExceptionMessage('Comment&#039;s too long. Please write more concisely.');

		// Asserting with value about 301 or more characters.
		$this->instance->filterPreprocessComment(['comment_content' => $this->faker->realTextBetween(300, 400)]);
	}
}
