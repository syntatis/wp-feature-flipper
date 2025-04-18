<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Comments;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\Str;

use function is_int;
use function is_scalar;
use function strip_tags;

use const PHP_INT_MAX;

final class CommentLength implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter('preprocess_comment', [$this, 'filterPreprocessComment'], PHP_INT_MAX);
	}

	/**
	 * Filter the comment content before it is processed.
	 *
	 * @param array{comment_content?:string|null} $commentData The comment data. {@see https://developer.wordpress.org/reference/hooks/preprocess_comment/}.
	 *
	 * @return array{comment_content?:string|null} The filtered comment data.
	 */
	public function filterPreprocessComment(array $commentData = []): array
	{
		if (! Option::isOn('comment_min_length_enabled')) {
			return $commentData;
		}

		if (isset($commentData['comment_content'])) {
			$minLength = Option::get('comment_min_length');
			$minLength = ! is_int($minLength) && is_scalar($minLength) ? absint($minLength) : null;

			if ($minLength === null) {
				return $commentData;
			}

			$length = Str::length(strip_tags($commentData['comment_content']));

			if (is_int($length) && $length < $minLength) {
				wp_die(
					esc_html(__('That comment\'s too short. Can you add something more helpful?', 'syntatis-feature-flipper')),
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
