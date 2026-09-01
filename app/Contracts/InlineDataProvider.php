<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Contracts;

use ArrayAccess;

/**
 * Allows a feature to contribute data to the plugin's inline data.
 *
 * The data is assembled privately inside the plugin: only the instances
 * the plugin registers as providers are called. External code cannot
 * hook into this flow, unlike a regular WordPress filter.
 */
interface InlineDataProvider
{
	/**
	 * Add or modify the plugin's inline data.
	 *
	 * @phpstan-param ArrayAccess<string,mixed> $data
	 *
	 * @phpstan-return ArrayAccess<string,mixed>
	 */
	public function inlineData(ArrayAccess $data): ArrayAccess;
}
