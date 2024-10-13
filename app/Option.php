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
}
