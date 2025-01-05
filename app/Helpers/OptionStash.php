<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use function array_diff;
use function array_filter;
use function array_intersect;
use function array_merge;
use function array_unique;
use function array_values;
use function count;
use function in_array;

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
	 * @param string           $name   Name of the option to update. Expected to not be SQL-escaped.
	 * @param array<int,mixed> $values Values to be added to the option.
	 * @phpstan-param non-empty-string $name
	 */
	public static function patchArray(string $name, array $values): bool
	{
		$currentValue = (array) Option::get($name);
		$stashedValue = self::get($name);

		// Replace current stashed value with the new values.
		self::update($name, $values);

		/**
		 * If the intersection between the values and the stashed values is empty,
		 * it means that the value is a complete new value.
		 */
		if (array_intersect($values, $stashedValue) === []) {
			// Simply update the option with the new values.
			return Option::update(
				$name,
				array_values(array_unique($values)),
			);
		}

		if (count($values) <= count($stashedValue)) {
			$currentValue = array_filter(
				$currentValue,
				static fn ($v) => in_array($v, $values, true),
			);
		}

		$newValue = array_diff($values, $stashedValue);
		$updatedValue = array_values(array_unique(array_merge($currentValue, $newValue)));

		return Option::update($name, $updatedValue);
	}

	/**
	 * Update the plugin stash option.
	 *
	 * @param string $name  Name of the option to update. Expected to not be SQL-escaped.
	 * @param mixed  $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 * @phpstan-param non-empty-string $name
	 */
	private static function update(string $name, $value): bool
	{
		return update_option(Option::name($name) . '_stash', $value);
	}
}
