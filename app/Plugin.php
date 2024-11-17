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
		$settings = $container->get(Settings::class);

		if ($settings instanceof Settings) {
			// Add the setting page.
			yield new SettingPage($settings);
		}

		// Apply the feature switches.
		yield new Switches\Admin();
		yield new Switches\Assets();
		yield new Switches\General();
		yield new Switches\Media();
		yield new Switches\Security();
		yield new Switches\Webpage();

		// Mark as initialized.
		do_action('syntatis/feature-flipper/init', $container);
	}
}
