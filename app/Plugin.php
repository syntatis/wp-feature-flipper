<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SFFV\Codex\Contracts\Extendable;
use SFFV\Codex\Settings\Settings;
use SFFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Modules\Modules;

final class Plugin implements Extendable
{
	/** @inheritDoc */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new Scripts();
		yield new SettingPage($container->get(Settings::class));

		/**
		 * Load and instantiate the plugin's modules.
		 *
		 * Instead of creating a loop, use `yield from` keyword to pass all items
		 * in the `Modules` to be iterated within this `Plugin` consumer.
		 *
		 * @see https://github.com/syntatis/codex/blob/main/app/Plugin.php
		 * @see https://www.php.net/manual/en/language.generators.syntax.php
		 */
		yield from new Modules($container);

		/**
		 * Runs after all the plugin's modules have been loaded and instantiated.
		 *
		 * @param ContainerInterface $container The dependency injection container.
		 */
		do_action('syntatis/feature_flipper/init', $container);
	}
}
