<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

class OptionStash
{
	/**
	 * Retrieve the value of the plugin stash option.
	 *
	 * @phpstan-param non-empty-string $name
	 *
	 * @return mixed
	 */
	public static function get(string $name)
	{
		return get_option(Option::name($name) . '_stash');
	}

	/**
	 * Add the plugin stash option.
	 *
	 * @param string $name  Name of the option to update. Expected to not be SQL-escaped.
	 * @param mixed  $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 * @phpstan-param non-empty-string $name
	 */
	public static function add(string $name, $value): bool
	{
		return add_option(Option::name($name) . '_stash', $value);
	}

	/**
	 * Update the plugin stash option.
	 *
	 * @param string $name  Name of the option to update. Expected to not be SQL-escaped.
	 * @param mixed  $value Option value. Must be serializable if non-scalar. Expected to not be SQL-escaped.
	 * @phpstan-param non-empty-string $name
	 */
	public static function update(string $name, $value): bool
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
}
