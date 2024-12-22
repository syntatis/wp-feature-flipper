<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use WP_Post_Type;

use function array_filter;
use function array_map;
use function in_array;

use const ARRAY_FILTER_USE_BOTH;

trait WithPostTypes
{
	/** @return array<string,array<string,mixed>> */
	private static function getPostTypes(): array
	{
		$postTypes = array_filter(
			get_post_types(['public' => true], 'objects'),
			static fn (WP_Post_Type $postTypeObject, string $postType) => ! in_array($postTypeObject->name, ['attachment'], true),
			ARRAY_FILTER_USE_BOTH,
		);

		return array_map(
			static fn (WP_Post_Type $postTypeObject): array => [
				'label' => $postTypeObject->label,
				'supports' => get_all_post_type_supports($postTypeObject->name),
			],
			$postTypes,
		);
	}
}
