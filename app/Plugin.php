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
		/** @var Settings $settings */
		$settings = $container->get(Settings::class);

		yield new CommonScripts();
		yield new SettingsPage($settings);
		yield from new Modules($container);

		// Mark as initialized.
		do_action('syntatis/feature_flipper/init', $container);
	}
}
