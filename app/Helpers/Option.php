<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use SSFV\Codex\Facades\Config;

use function array_diff;
use function array_filter;
use function array_intersect;
use function array_unique;
use function array_values;
use function count;
use function in_array;
use function is_array;

final class Option
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
	 * Retrieve the value of the plugin option.
	 *
	 * @phpstan-param non-empty-string $name
	 *
	 * @return mixed
	 */
	public static function get(string $name)
	{
		return get_option(self::name($name));
	}

	/**
	 * Check whether a plugin option is enabled.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	public static function isOn(string $name): bool
	{
		return in_array(self::get($name), [true, '1'], true);
	}

	/**
	 * Update the plugin option.
	 *
	 * @param string $name  Name of the option to update. Expected to not be SQL-escaped.
	 * @param mixed  $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 * @phpstan-param non-empty-string $name
	 */
	public static function update(string $name, $value): bool
	{
		return update_option(self::name($name), $value);
	}

	/**
	 * Add the plugin option.
	 *
	 * @param string $name  Name of the option to update. Expected to not be SQL-escaped.
	 * @param mixed  $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 * @phpstan-param non-empty-string $name
	 */
	public static function add(string $name, $value): bool
	{
		return add_option(self::name($name), $value);
	}

	/**
	 * Delete the plugin option.
	 *
	 * @param string $name Name of the option to update. Expected to not be SQL-escaped.
	 * @phpstan-param non-empty-string $name
	 */
	public static function delete(string $name): bool
	{
		return delete_option(self::name($name));
	}

	/**
	 * Retrieve the option name with th prefix.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	public static function name(string $name): string
	{
		return Config::get('app.option_prefix') . $name;
	}

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
	 *
	 * @see \Syntatis\FeatureFlipper\Helpers\Option::patch()
	 *
	 * @template TKey of array-key
	 * @template TValue
	 *
	 * @phpstan-param non-empty-string $name
	 * @phpstan-param array<TKey,TValue> $source
	 */
	public static function stash(string $name, array $source): bool
	{
		return update_option('_' . self::name($name) . '_stash', $source);
	}

	/**
	 * Patch the plugin option value with the current source.
	 *
	 * @see Syntatis\FeatureFlipper\Helpers\Option::stash()
	 *
	 * @template TKey of array-key
	 * @template TValue
	 *
	 * @param string $name  The name of the option.
	 * @param array  $value The current value of the option.
	 * @phpstan-param non-empty-string $name
	 * @phpstan-param array<TKey,TValue> $value
	 * @phpstan-param array<TKey,TValue> $source
	 *
	 * @phpstan-return array<TKey,TValue>
	 *
	 * @internal This method is typically used within the option hook where the current
	 *           value of the option is provided.
	 */
	public static function patch(string $name, array $value, array $source): array
	{
		$stashed = get_option('_' . self::name($name) . '_stash');

		if (! is_array($stashed) || $source === $stashed) {
			return $value;
		}

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

		return array_values(
			array_unique([
				...$value,
				...array_diff($source, $stashed), // New value.
			]),
		);
	}
}
