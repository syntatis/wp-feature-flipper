<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use Faker\Factory;
use Faker\Generator;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\CommentLength;
use Syntatis\FeatureFlipper\Features\Comments;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\General;
use Syntatis\Tests\WithAdminBar;
use Syntatis\Tests\WPTestCase;
use WPDieException;

use const PHP_INT_MIN;

/**
 * @group feature-comments
 * @group module-general
 */
class CommentLengthTest extends WPTestCase
{
	use WithAdminBar;

	private Hook $hook;
	private CommentLength $instance;
	private Generator $faker;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new CommentLength(true, true);
		$this->instance->hook($this->hook);
		$this->faker = Factory::create();
	}

	/** @testdox should have callback attached to hooks */
	public function testHook(): void
	{
		$this->assertSame(PHP_INT_MIN, $this->hook->hasFilter('preprocess_comment', [$this->instance, 'filterPreprocessComment']));
	}

	/**
	 * @group feature-comments
	 * @testdox should return the comment value as is when the comment min length is disabled
	 */
	public function testMinCommentLengthDisabled(): void
	{
		$this->assertFalse(Option::isOn('comment_min_length_enabled'));

		$comment = $this->instance->filterPreprocessComment(['comment_content' => 'Hi!']); // 3 characters.

		$this->assertEquals(['comment_content' => 'Hi!'], $comment);
	}

	/**
	 * @group feature-comments
	 * @testdox should return the comment value or fail the comment is too short (default: 10 characters)
	 */
	public function testMinCommentLengthEnabled(): void
	{
		Option::update('comment_min_length_enabled', true);

		$this->assertTrue(Option::isOn('comment_min_length_enabled'));
		$this->assertEquals(
			['comment_content' => 'hello world!'],
			$this->instance->filterPreprocessComment(['comment_content' => 'hello world!']), // 12 characters.
		);

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
		Option::update('comment_min_length_enabled', true);
		Option::update('comment_min_length', 20);

		$this->assertTrue(Option::isOn('comment_min_length_enabled'));
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
	 * @testdox should return the comment value as is when the comment max length is disabled
	 */
	public function testMaxCommentLengthDisabled(): void
	{
		$this->assertFalse(Option::isOn('comment_max_length_enabled'));

		$text = $this->faker->realTextBetween(101, 200);
		$comment = $this->instance->filterPreprocessComment(['comment_content' => $text]);

		$this->assertEquals(['comment_content' => $text], $comment);
	}

	/**
	 * @group feature-comments
	 * @testdox should return the comment value or fail when the comment is too long (default: 100 characters)
	 */
	public function testMaxCommentLengthEnabled(): void
	{
		Option::update('comment_max_length_enabled', true);

		$this->assertTrue(Option::isOn('comment_max_length_enabled'));
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
		Option::update('comment_max_length_enabled', true);
		Option::update('comment_max_length', 300);

		$this->assertTrue(Option::isOn('comment_max_length_enabled'));
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
