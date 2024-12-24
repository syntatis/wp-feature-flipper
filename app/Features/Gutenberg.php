<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Concerns\WithPostTypes;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Post;

use function array_filter;
use function array_keys;
use function array_values;
use function in_array;
use function is_int;

use const PHP_INT_MAX;

class Gutenberg implements Hookable
{
	use WithHookName;
	use WithPostTypes;

	public function hook(Hook $hook): void
	{
		$hook->addFilter('use_block_editor_for_post', [$this, 'filterUseBlockEditorForPost'], PHP_INT_MAX, 2);
		$hook->addFilter(
			self::defaultOptionName('gutenberg_post_types'),
			static fn () => array_values(array_filter(
				array_keys(self::getRegisteredPostTypes()),
				static fn ($key): bool => use_block_editor_for_post_type($key),
			)),
			PHP_INT_MAX,
		);
	}

	/**
	 * Filter the value to determine whether to use the block editor for a post.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/use_block_editor_for_post/
	 *
	 * @param int|WP_Post $post
	 */
	public function filterUseBlockEditorForPost(bool $value, $post): bool
	{
		// If the Gutenberg feature is disabled, force the classic editor.
		if (! (bool) Option::get('gutenberg')) {
			return false;
		}

		if (is_int($post)) {
			$post = get_post($post);
		}

		if ($post === null) {
			return $value;
		}

		return in_array(
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress convention.
			$post->post_type,
			(array) Option::get('gutenberg_post_types'),
			true,
		);
	}
}
