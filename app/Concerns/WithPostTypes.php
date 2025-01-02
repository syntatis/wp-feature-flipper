<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use WP_Post_Type;

use function array_filter;
use function array_map;
use function in_array;

use const ARRAY_FILTER_USE_BOTH;

/**
 * A collection of methods to work with post types.
 */
trait WithPostTypes
{
	/**
	 * Retrieve the list of registered post types on the site.
	 *
	 * @see register_post_type() for accepted arguments.
	 *
	 * @param array<string,mixed> $args Optional. An array of key => value arguments to match against
	 *                                  the post type objects.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private static function getRegisteredPostTypes(array $args = ['public' => true]): array
	{
		$postTypes = array_filter(
			get_post_types($args, 'objects'),
			static fn (WP_Post_Type $postTypeObject, string $postType) => ! in_array($postTypeObject->name, ['attachment'], true),
			ARRAY_FILTER_USE_BOTH,
		);

		return array_map(
			static fn (WP_Post_Type $postTypeObject): array => [
				'name' => $postTypeObject->name,
				'label' => $postTypeObject->label,
				'supports' => get_all_post_type_supports($postTypeObject->name),
			],
			$postTypes,
		);
	}
}
