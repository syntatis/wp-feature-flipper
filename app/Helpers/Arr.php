<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use function array_values;

class Arr
{
	/**
	 * Validates that the provided array is a list (i.e. it has only numeric keys
	 * starting from 0 and increasing by 1 for each element).
	 *
	 * @param array<mixed> $array
	 *
	 * @phpstan-assert-if-true list<mixed> $array
	 * @phpstan-assert-if-false array<string,mixed> $array
	 * @psalm-assert-if-true list<mixed> $array
	 * @psalm-assert-if-false array<string,mixed> $array
	 */
	public static function isList(array $array): bool
	{
		if ($array === [] || $array === array_values($array)) {
			return true;
		}

		$nextKey = -1;

		foreach ($array as $k => $v) {
			if ($k !== ++$nextKey) {
				return false;
			}
		}

		return true;
	}
}
