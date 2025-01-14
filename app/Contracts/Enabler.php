<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Contracts;

interface Enabler
{
	public function isEnabled(): bool;
}
