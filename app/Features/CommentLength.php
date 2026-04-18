<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\Str;

use function is_numeric;
use function strip_tags;

use const PHP_INT_MIN;

final class CommentLength implements Hookable
{
	private bool $minLengthEnabled;
	private bool $maxLengthEnabled;

	public function __construct(bool $minLengthEnabled, bool $maxLengthEnabled)
	{
		$this->minLengthEnabled = $minLengthEnabled;
		$this->maxLengthEnabled = $maxLengthEnabled;
	}

	public function hook(Hook $hook): void
	{
		$hook->addFilter('preprocess_comment', [$this, 'filterPreprocessComment'], PHP_INT_MIN);
	}

	/**
	 * Filter the comment content before it is processed.
	 *
	 * @param array{comment_content?:string|null} $commentData The comment data.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/preprocess_comment/
	 *
	 * @return array{comment_content?:string|null} The filtered comment data.
	 */
	public function filterPreprocessComment(array $commentData = []): array
	{
		if (! isset($commentData['comment_content'])) {
			return $commentData;
		}

		$length = Str::length(
			strip_tags($commentData['comment_content']),
			get_bloginfo('charset'),
		);

		if ($length === false) {
			return $commentData;
		}

		if ($this->minLengthEnabled) {
			$minLength = Option::get('comment_min_length');
			$minLength = is_numeric($minLength) ? absint($minLength) : null;

			if ($minLength !== null && $length < $minLength) {
				wp_die(
					esc_html(__('Comment\'s too short. Please write something more helpful.', 'syntatis-feature-flipper')),
					esc_html(__('Comment Error', 'syntatis-feature-flipper')),
					[
						'response' => 400,
						'back_link' => true,
					],
				);
			}
		}

		if ($this->maxLengthEnabled) {
			$maxLength = Option::get('comment_max_length');
			$maxLength = is_numeric($maxLength) ? absint($maxLength) : null;

			if ($maxLength !== null && $length > $maxLength) {
				wp_die(
					esc_html(__('Comment\'s too long. Please write more concisely.', 'syntatis-feature-flipper')),
					esc_html(__('Comment Error', 'syntatis-feature-flipper')),
					[
						'response' => 400,
						'back_link' => true,
					],
				);
			}
		}

		return $commentData;
	}
}
