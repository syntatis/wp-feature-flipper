<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Settings\Settings;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Modules\Modules;

final class Plugin implements Extendable
{
	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new CommonScripts();
		yield new SettingPage($container->get(Settings::class));
		yield from new Modules($container);

		// Mark as initialized.
		do_action('syntatis/feature_flipper/init', $container);
	}
}
