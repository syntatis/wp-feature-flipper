<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use Syntatis\FeatureFlipper\Concerns\DontInstantiate;

use function strlen;
use function substr_compare;

final class Str
{
	use DontInstantiate;

	public static function endsWith(string $haystack, string $needle): bool
	{
		if ($needle === '' || $needle === $haystack) {
			return true;
		}

		if ($haystack === '') {
			return false;
		}

		$needleLength = strlen($needle);

		return $needleLength <= strlen($haystack) && substr_compare($haystack, $needle, -$needleLength) === 0;
	}
}
