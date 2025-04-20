<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use Syntatis\FeatureFlipper\Concerns\DontInstantiate;

use function function_exists;
use function mb_strlen;
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

	public static function length(string $value, string $encoding = 'UTF-8'): int
	{
		if (function_exists('mb_strlen')) {
			return mb_strlen($value, $encoding);
		}

		return strlen($value);
	}
}
