<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use Syntatis\FeatureFlipper\Contracts\Enable;
use Syntatis\FeatureFlipper\Option;

class AutoUpdateComponents implements Enable
{
	/** @param bool $value Current value of the option passed from the `option_` filter argument. */
	public function isEnabled(bool $value): bool
	{
		if (! (bool) Option::get('updates')) {
			return false;
		}

		if (! (bool) Option::get('auto_updates')) {
			return false;
		}

		return $value;
	}
}
