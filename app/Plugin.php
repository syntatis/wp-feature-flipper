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
		// Add the setting page.
		yield new SettingPage($container->get(Settings::class));

		// Apply the feature switches.
		yield new Switches\Admin();
		yield new Switches\Assets();
		yield new Switches\General();
		yield new Switches\Media();
		yield new Switches\Security();
		yield new Switches\Webpage();

		// Mark as initilized.
		do_action('syntatis/feature-flipper/init', $container);
	}
}
