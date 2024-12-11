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
		$switches = $this->getSwitches();

		if ($settings instanceof Settings) {
			yield new SettingPage($settings);
		}

		foreach ($switches as $switch) {
			yield $switch;

			if (! ($switch instanceof Extendable)) {
				continue;
			}

			yield from $switch->getInstances($container);
		}

		// Mark as initialized.
		do_action('syntatis/feature_flipper/init', $container);
	}

	/** @return iterable<object> */
	private function getSwitches(): iterable
	{
		yield new Switches\Admin();
		yield new Switches\Assets();
		yield new Switches\General();
		yield new Switches\Media();
		yield new Switches\Security();
		yield new Switches\Site();
	}
}
