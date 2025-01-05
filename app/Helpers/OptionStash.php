<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use function array_merge;
use function array_unique;

class OptionStash
{
	/**
	 * Retrieve the value of the plugin stash option.
	 *
	 * @phpstan-param non-empty-string $name
	 *
	 * @return array<array-key,mixed>
	 */
	public static function get(string $name): array
	{
		return (array) get_option(Option::name($name) . '_stash');
	}

	/**
	 * Delete the plugin stash option.
	 *
	 * @param string $name Name of the option to update. Expected to not be SQL-escaped.
	 * @phpstan-param non-empty-string $name
	 */
	public static function delete(string $name): bool
	{
		return delete_option(Option::name($name) . '_stash');
	}

	/**
	 * Patch value of the plugin stash option.
	 *
	 * @param string                 $name   Name of the option to update. Expected to not be SQL-escaped.
	 * @param array<array-key,mixed> $values Values to be added to the option.
	 * @phpstan-param non-empty-string $name
	 */
	public static function patch(string $name, array $values): bool
	{
		$currentValue = Option::get($name);
		$stashedValue = self::get(Option::name($name) . '_stash');

		$optionValue = array_merge($optionValue, $value);
		$optionValue = array_unique($optionValue);

		return update_option($optionName, $optionValue);
	}
}
