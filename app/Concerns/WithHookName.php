<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use Syntatis\FeatureFlipper\Helpers\Option;

trait WithHookName
{
	/**
	 * Retrieve the plugin option name filter, added with the prefix.
	 *
	 * @see inc/config/app.php For the option prefix.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function optionHook(string $name): string
	{
		return 'option_' . Option::name($name);
	}

	/**
	 * Retrieve the plugin default option name filter, added with the prefix.
	 *
	 * @see inc/config/app.php For the option prefix.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function defaultOptionHook(string $name): string
	{
		return 'default_option_' . Option::name($name);
	}

	/**
	 * Retrieve the plugin update option filter name, added with the prefix.
	 *
	 * The filter runs after the option is successfully updated.
	 *
	 * @see inc/config/app.php For the option prefix.
	 * @see https://github.com/WordPress/WordPress/blob/master/wp-includes/option.php#L1011 Where the hook is applied.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function updateOptionHook(string $name): string
	{
		return 'update_option_' . Option::name($name);
	}

	/**
	 * Retrieve the plugin add option filter name, added with the prefix.
	 *
	 * The filter runs after the option is successfully updated.
	 *
	 * @see inc/config/app.php For the option prefix.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function addOptionHook(string $name): string
	{
		return 'add_option_' . Option::name($name);
	}
}
