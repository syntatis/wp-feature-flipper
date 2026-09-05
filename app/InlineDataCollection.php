<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use Syntatis\FeatureFlipper\Contracts\InlineDataProvider;

/**
 * Collects the features that contribute data to the plugin's inline data.
 */
final class InlineDataCollection
{
	/** @var list<InlineDataProvider> */
	private array $providers = [];

	public function add(InlineDataProvider $provider): void
	{
		$this->providers[] = $provider;
	}

	/** @return list<InlineDataProvider> */
	public function all(): array
	{
		return $this->providers;
	}
}
