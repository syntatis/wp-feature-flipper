<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use Syntatis\FeatureFlipper\Contracts\Enable;

/**
 * @see https://developer.wordpress.org/reference/hooks/option_option/
 *
 * @internal Methods in this function should be called within the `option_*` hook.
 */
class AutoUpdate implements Enable
{
	private function __construct()
	{
	}

	public static function global(): Enable
	{
		return new self();
	}

	public static function core(): Enable
	{
		return self::components();
	}

	public static function plugins(): Enable
	{
		return self::components();
	}

	public static function themes(): Enable
	{
		return self::components();
	}

	private static function components(): Enable
	{
		return new AutoUpdateComponents();
	}

	/** @param bool $value Current value of the option passed from the `option_` filter argument. */
	public function isEnabled(bool $value): bool
	{
		if (! (bool) Option::get('updates')) {
			return false;
		}

		return $value;
	}
}
