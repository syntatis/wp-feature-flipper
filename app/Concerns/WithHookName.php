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
	private static function optionName(string $name): string
	{
		return 'option_' . Option::name($name);
	}

	/**
	 * Retrieve the plugin option name filter, added with the prefix.
	 *
	 * @see inc/config/app.php For the option prefix.
	 *
	 * @phpstan-param non-empty-string $name
	 */
	private static function defaultOptionName(string $name): string
	{
		return 'default_option_' . Option::name($name);
	}
}
