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
class Stash
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
	private static function get(string $name): array
	{
		return (array) get_option('_' . Option::name($name) . '_stash');
	}

	/**
	 * Update the plugin stash option.
	 *
	 * @param string $name  Name of the option to update. Expected to not be SQL-escaped.
	 * @param array  $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 * @phpstan-param non-empty-string $name
	 * @phpstan-param array<array-key,mixed> $value
	 */
	private static function update(string $name, array $value): bool
	{
		return update_option('_' . Option::name($name) . '_stash', $value);
	}

	/**
	 * @template TKey of array-key
	 * @template TValue
	 *
	 * @phpstan-param non-empty-string $name
	 * @phpstan-param array<TKey,TValue> $value
	 * @phpstan-param array<TKey,TValue> $source
	 *
	 * @phpstan-return array<TKey,TValue>
	 */
	public static function patch(string $name, array $value, array $source): array
	{
		$stashed = self::get($name);

		if ($source === $stashed) {
			return $value;
		}

		// Replace current stashed value with the new values.
		self::update($name, $source);

		/**
		 * If the intersection between the values and the stashed values is empty,
		 * it means that the value is a complete new value.
		 */
		if (array_intersect($source, $stashed) === []) {
			return $source;
		}

		if (count($source) <= count($stashed)) {
			$value = array_filter($value, static fn ($v) => in_array($v, $source, true));
		}

		$newValue = array_diff($source, $stashed);

		return array_values(array_unique([...$value, ...$newValue]));
	}
}
