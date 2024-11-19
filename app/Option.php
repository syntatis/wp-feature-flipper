<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Facades\Config;

class Option
{
	/**
	 * Retrieve the value of the plugin option.
	 *
	 * @return mixed
	 */
	public static function get(string $name)
	{
		return get_option(Config::get('app.option_prefix') . $name);
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
