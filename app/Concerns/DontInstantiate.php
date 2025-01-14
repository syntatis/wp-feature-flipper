<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

trait DontInstantiate
{
	/**
	 * Prevent instantiation.
	 *
	 * @codeCoverageIgnore
	 */
	final private function __construct()
	{
	}
}
