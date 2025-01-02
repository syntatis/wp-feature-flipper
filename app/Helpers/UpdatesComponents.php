<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use Syntatis\FeatureFlipper\Contracts\Enable;

class UpdatesComponents implements Enable
{
	/** @param bool $value Current value of the option passed from the `option_` filter argument. */
	public function isEnabled(bool $value): bool
	{
		if (! Option::isOn('updates')) {
			return false;
		}

		return $value;
	}
}
