<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use function array_diff;
use function array_filter;
use function array_intersect;
use function array_unique;
use function array_values;
use function count;
use function in_array;
use function is_array;

/**
 * A stash option is a special type of plugin option. It is used to track
 * changes (like additions or removals) to arrays or objects value but
 * won't be used directly by the plugin.
 *
 * For example, consider a plugin option named `adminbar_menu`. The option
 * controls which menus are displayed in the admin bar. If it currently
 * returns ['a', 'b', 'c'], only these menus is going to be shown in
 * the admin bar.
 *
 * The issue arises when another plugin adds a new menu to the admin bar.
 * Since it can't directly modify the `adminbar_menu` option, the new
 * menu won't appear, which is often not the behavior a user would
 * expect.
 *
 * This is where stash option is designed for. It tracks the full list of
 * menus that should be displayed in the admin bar.
 *
 * While the `adminbar_menu` option is used to store menus that should be
 * displayed, the corresponding stash option, will keep track the full
 * list of the menu available. It helps to determine which ones are
 * new, which one should be  removed, and which one should still
 * be there.
 *
 * This ensure updates are reflected accordingly, so menus added by other
 * plugins won't be hidden by default.
 */
class OptionStash
{
	/**
	 * Prevent instantiation.
	 *
	 * @codeCoverageIgnore
	 */
	final private function __construct()
	{
	}

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
	 * Patch the value of a stash option.
	 *
	 * @param string                 $name   Name of the option to update. Expected to not be SQL-escaped.
	 * @param array<array-key,mixed> $values Values to be added to the option.
	 * @phpstan-param non-empty-string $name
	 */
	public static function patch(string $name, array $values): void
	{
		if (! Arr::isList($values)) {
			return;
		}

		self::patchList($name, $values);
	}

	/**
	 * Patch list value of the plugin stash option.
	 *
	 * @param string           $name   Name of the option to update. Expected to not be SQL-escaped.
	 * @param array<int,mixed> $values Values to be added to the option.
	 * @phpstan-param non-empty-string $name
	 * @phpstan-param list<mixed> $values
	 */
	private static function patchList(string $name, array $values): void
	{
		$currentStashedValue = self::get($name);

		if ($currentStashedValue === $values) {
			return;
		}

		// Replace current stashed value with the new values.
		self::update($name, $values);

		$currentValue = Option::get($name);

		if (! is_array($currentValue)) {
			return;
		}

		/**
		 * If the intersection between the values and the stashed values is empty,
		 * it means that the value is a complete new value.
		 */
		if (array_intersect($values, $currentStashedValue) === []) {
			Option::update(
				$name,
				array_values(array_unique($values)),
			);

			return;
		}

		if (count($values) <= count($currentStashedValue)) {
			$currentValue = array_filter(
				$currentValue,
				static fn ($v) => in_array($v, $values, true),
			);
		}

		$newValue = array_diff($values, $currentStashedValue);
		$updatedValue = array_values(array_unique([...$currentValue, ...$newValue]));

		Option::update($name, $updatedValue);
	}
}
