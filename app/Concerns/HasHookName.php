<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use Syntatis\FeatureFlipper\Helpers\Option;

trait HasHookName
{
	/** @phpstan-param non-empty-string $name */
	private static function optionName(string $name): string
	{
		return 'option_' . Option::name($name);
	}

	/** @phpstan-param non-empty-string $name */
	private static function defaultOptionName(string $name): string
	{
		return 'default_option_' . Option::name($name);
	}
}
