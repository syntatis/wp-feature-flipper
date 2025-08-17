<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Settings\Settings;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\Modules;

final class Plugin implements Extendable
{
	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		Option::primeCache($container->get(Settings::class));

		yield new CommonScripts();
		yield new SettingPage($container->get(Settings::class));
		yield from new Modules($container);

		do_action('syntatis/feature_flipper/init', $container);
	}
}
