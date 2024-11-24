<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Contracts;

interface Enable
{
	/** @param bool $value The currrent value, typically passed from from a filter. */
	public function isEnabled(bool $value): bool;
}
