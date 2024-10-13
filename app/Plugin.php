<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Settings\Settings;
use SSFV\Psr\Container\ContainerInterface;

class Plugin implements Extendable
{
	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new SettingPage($container->get(Settings::class));

		// Switches.
		yield new Switches\Admin();
		yield new Switches\Assets();
		yield new Switches\General();
		yield new Switches\Media();
	}
}
