<?php

declare(strict_types=1);

namespace Syntatis\Tests;

use SFFV\Pimple\Container;
use SFFV\Pimple\Psr11\Container as Psr11Container;
use Syntatis\FeatureFlipper\Features\AdminBar\AdminBar;
use Syntatis\FeatureFlipper\Features\Autosave;
use Syntatis\FeatureFlipper\Features\TrashRetention;
use Syntatis\FeatureFlipper\InlineDataCollection;
use Syntatis\FeatureFlipper\Modules\Modules;

use function array_map;
use function get_class;
use function iterator_to_array;

/** @group modules */
class ModulesTest extends WPTestCase
{
	/** @testdox should collect the inline data providers from the instance graph */
	public function testCollectInlineDataProviders(): void
	{
		$pimple = new Container();
		$pimple[InlineDataCollection::class] = static fn (): InlineDataCollection => new InlineDataCollection();

		$container = new Psr11Container($pimple);

		iterator_to_array(new Modules($container), false);

		$classes = array_map(
			static fn (object $provider): string => get_class($provider),
			$container->get(InlineDataCollection::class)->all(),
		);

		$this->assertContains(Autosave::class, $classes);
		$this->assertContains(TrashRetention::class, $classes);
		$this->assertContains(AdminBar::class, $classes);
	}
}
