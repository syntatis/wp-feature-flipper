<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use Syntatis\FeatureFlipper\Helpers\Option;

trait WithHookName
{
	/**
	 * Retrieve option name filter, added with plugin the prefix.
	 *
	 * @see inc/config/app.php For the option prefix.
	 * @see https://developer.wordpress.org/reference/hooks/option_option/ For the hook documentation.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function optionHook(string $name): string
	{
		return 'option_' . Option::name($name);
	}

	/**
	 * Retrieve default option name filter, added with plugin the prefix.
	 *
	 * @see inc/config/app.php For the option prefix.
	 * @see https://developer.wordpress.org/reference/hooks/default_option_option/ For the hook documentation.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function defaultOptionHook(string $name): string
	{
		return 'default_option_' . Option::name($name);
	}

	/**
	 * Retrieve update option filter name, added with plugin the prefix.
	 *
	 * @see inc/config/app.php For the option prefix.
	 * @see https://developer.wordpress.org/reference/hooks/update_option_option/For the hook documentation.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function updateOptionHook(string $name): string
	{
		return 'update_option_' . Option::name($name);
	}

	/**
	 * Retrieve add option filter name, added with plugin the prefix.
	 *
	 * @see inc/config/app.php For the option prefix.
	 * @see https://developer.wordpress.org/reference/hooks/add_option_option/ For the hook documentation.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function addOptionHook(string $name): string
	{
		return 'add_option_' . Option::name($name);
	}
}
