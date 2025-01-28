<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Post;

use function array_filter;
use function array_values;
use function in_array;
use function is_array;
use function is_int;

use const PHP_INT_MAX;

final class Gutenberg implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addAction('syntatis/feature_flipper/updated_options', [$this, 'stashOptions']);
		$hook->addFilter('use_block_editor_for_post', [$this, 'filterUseBlockEditorForPost'], PHP_INT_MAX, 2);
		$hook->addFilter(
			Option::hook('default:gutenberg_post_types'),
			static fn () => self::getPostTypes(),
			PHP_INT_MAX,
		);
		$hook->addFilter(
			Option::hook('gutenberg_post_types'),
			static fn ($value) => Option::patch(
				'gutenberg_post_types',
				is_array($value) ? $value : [],
				self::getPostTypes(),
			),
			PHP_INT_MAX,
		);
	}

	/** @param array<string> $options List of option names that have been updated. */
	public function stashOptions(array $options): void
	{
		if (! in_array(Option::name('gutenberg_post_types'), $options, true)) {
			return;
		}

		Option::stash('gutenberg_post_types', self::getPostTypes());
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
		if (! Option::isOn('gutenberg')) {
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

	/** @return array<string> */
	private static function getPostTypes(): array
	{
		return array_values(array_filter(
			get_post_types(['public' => true]),
			static fn ($key): bool => use_block_editor_for_post_type($key),
		));
	}
}
