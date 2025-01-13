<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates\Helpers;

use Syntatis\FeatureFlipper\Contracts\Enabler;

/**
 * @see https://developer.wordpress.org/reference/hooks/option_option/
 *
 * @internal Methods in this function should be called within the `*option_*` hooks.
 */
final class Updates implements Enabler
{
	private bool $value;

	/** @param bool $value Current value of the option passed from the `option_` filter argument. */
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
		return new UpdatesComponents($value);
	}

	public function isEnabled(): bool
	{
		return $this->value;
	}
}
