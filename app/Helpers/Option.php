<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use SSFV\Codex\Facades\Config;

class Option
{
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
		return (bool) self::get($name);
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
}
