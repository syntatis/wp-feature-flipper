<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Providers;

use SFFV\Codex\Abstracts\ServiceProvider;
use SFFV\Pimple\Container;
use Syntatis\FeatureFlipper\InlineData;
use Syntatis\FeatureFlipper\InlineDataCollection;

/**
 * Registers the plugin's inline data as a container service.
 */
final class InlineDataServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->container[InlineDataCollection::class] = static fn (): InlineDataCollection => new InlineDataCollection();
		$this->container[InlineData::class] = static function (Container $container): InlineData {
			/** @var InlineDataCollection $collection */
			$collection = $container[InlineDataCollection::class];

			return new InlineData($collection->all());
		};
	}
}
