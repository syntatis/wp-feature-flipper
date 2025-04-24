<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Action;
use SSFV\Codex\Foundation\Hooks\Filter;
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
		$hook->parse($this);
		$hook->addFilter(
			Option::hook('default:gutenberg_post_types'),
			static fn () => self::getPostTypes(),
			PHP_INT_MAX,
		);
		$hook->addFilter(
			Option::hook('gutenberg_post_types'),
			static fn (mixed $value) => Option::patch(
				'gutenberg_post_types',
				is_array($value) ? $value : [],
				self::getPostTypes(),
			),
			PHP_INT_MAX,
		);
	}

	/** @param array<string> $options List of option names that have been updated. */
	#[Action(name: 'syntatis/feature_flipper/updated_options', priority: PHP_INT_MAX)]
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
	 */
	#[Filter(name: 'use_block_editor_for_post', priority: PHP_INT_MAX, acceptedArgs: 2)]
	public function useBlockEditorForPost(bool $value, int|WP_Post $post): bool
	{
		// If the Gutenberg feature is disabled, force the classic editor.
		if (! Option::isOn('gutenberg')) {
			return false;
		}

		$wpPost = is_int($post) ? get_post($post) : $post;

		if (! $wpPost instanceof WP_Post) {
			return $value;
		}

		$postTypes = Option::get('gutenberg_post_types');
		$postTypes = is_array($postTypes) ? $postTypes : [];

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress convention.
		return in_array($wpPost->post_type, $postTypes, true);
	}

	/** @phpstan-return list<string> */
	private static function getPostTypes(): array
	{
		return array_values(array_filter(
			get_post_types(['public' => true]),
			static fn (string $key): bool => use_block_editor_for_post_type($key),
		));
	}
}
