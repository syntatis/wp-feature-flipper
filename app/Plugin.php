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
		$modules = $this->getModules();

		yield new CommonScripts();

		if ($settings instanceof Settings) {
			yield new SettingPage($settings);
		}

		foreach ($modules as $module) {
			yield $module;

			if (! ($module instanceof Extendable)) {
				continue;
			}

			yield from $module->getInstances($container);
		}

		// Mark as initialized.
		do_action('syntatis/feature_flipper/init', $container);
	}

	/** @return iterable<object> */
	private function getModules(): iterable
	{
		yield new Modules\Admin();
		yield new Modules\General();
		yield new Modules\Media();
		yield new Modules\Security();
		yield new Modules\Site();
	}
}
