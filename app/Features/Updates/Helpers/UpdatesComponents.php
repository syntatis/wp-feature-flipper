<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates\Helpers;

use Syntatis\FeatureFlipper\Contracts\Enabler;
use Syntatis\FeatureFlipper\Helpers\Option;

final class UpdatesComponents implements Enabler
{
	private bool $value;

	/** @param bool $value Current value of the option passed from the `option_` filter argument. */
	public function __construct(bool $value)
	{
		$this->value = $value;
	}

	public function isEnabled(): bool
	{
		if (! Option::isOn('updates')) {
			return false;
		}

		return $this->value;
	}
}
