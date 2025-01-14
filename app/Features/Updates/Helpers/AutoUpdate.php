<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates\Helpers;

use Syntatis\FeatureFlipper\Contracts\Enabler;
use Syntatis\FeatureFlipper\Helpers\Option;

/**
 * @see https://developer.wordpress.org/reference/hooks/option_option/
 *
 * @internal Methods in this function should be called within the `*option_*` hooks.
 */
final class AutoUpdate implements Enabler
{
	private bool $value;

	private function __construct(bool $value)
	{
		$this->value = $value;
	}

	public static function global(bool $value): Enabler
	{
		return new self($value);
	}

	public static function components(bool $value): Enabler
	{
		return new AutoUpdateComponents($value);
	}

	public function isEnabled(): bool
	{
		if (! Option::isOn('updates')) {
			return false;
		}

		return $this->value;
	}
}
