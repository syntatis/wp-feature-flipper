<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use Syntatis\FeatureFlipper\Helpers\Option;

/**
 * A collection of reusable methods to retrieve WordPress hook names.
 */
trait WithHookName
{
	/**
	 * Retrieve the option name filter, added with plugin the prefix.
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
	 * Retrieve the default option name filter, added with plugin the prefix.
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
	 * Retrieve the update option filter name, added with plugin the prefix.
	 *
	 * @see inc/config/app.php For the option prefix.
	 * @see https://developer.wordpress.org/reference/hooks/update_option_option/ For the hook documentation.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function updateOptionHook(string $name): string
	{
		return 'update_option_' . Option::name($name);
	}

	/**
	 * Retrieve the add option filter name, added with plugin the prefix.
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

	/**
	 * Retrieve the delete option filter name, added with plugin the prefix.
	 *
	 * @see inc/config/app.php For the option prefix.
	 * @see https://developer.wordpress.org/reference/hooks/delete_option_option/ For the hook documentation.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function deleteOptionHook(string $name): string
	{
		return 'delete_option_' . Option::name($name);
	}

	/**
	 * Retrieve the sanitize option filter name, added with plugin the prefix.
	 *
	 * @see inc/config/app.php For the option prefix.
	 * @see https://developer.wordpress.org/reference/hooks/sanitize_option_option/
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function sanitizeOptionHook(string $name): string
	{
		return 'sanitize_option_' . Option::name($name);
	}
}
