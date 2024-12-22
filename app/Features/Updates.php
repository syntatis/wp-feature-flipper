<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Features\Updates\ManageCore;
use Syntatis\FeatureFlipper\Features\Updates\ManageGlobal;
use Syntatis\FeatureFlipper\Features\Updates\ManagePlugins;
use Syntatis\FeatureFlipper\Features\Updates\ManageThemes;
use Syntatis\FeatureFlipper\Helpers;

class Updates implements Hookable, Extendable
{
	use WithHookName;

	public function hook(Hook $hook): void
	{
		/**
		 * Globals
		 */
		$hook->addFilter(
			self::optionName('updates'),
			static fn ($value) => Helpers\Updates::global()->isEnabled((bool) $value),
		);
		$hook->addFilter(
			self::optionName('auto_updates'),
			static fn ($value) => Helpers\AutoUpdate::global()->isEnabled((bool) $value),
		);

		// Components: Core
		$updatesFn = static fn ($value) => Helpers\Updates::core()->isEnabled((bool) $value);
		$autoUpdateFn = static fn ($value) => Helpers\AutoUpdate::core()->isEnabled((bool) $value);

		$hook->addFilter(self::defaultOptionName('auto_update_core'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionName('update_core'), $updatesFn);
		$hook->addFilter(self::optionName('auto_update_core'), $autoUpdateFn);
		$hook->addFilter(self::optionName('update_core'), $updatesFn);

		// Components: Plugins
		$updatesFn = static fn ($value) => Helpers\Updates::plugins()->isEnabled((bool) $value);
		$autoUpdateFn = static fn ($value) => Helpers\AutoUpdate::plugins()->isEnabled((bool) $value);

		$hook->addFilter(self::defaultOptionName('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionName('update_plugins'), $updatesFn);
		$hook->addFilter(self::optionName('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(self::optionName('update_plugins'), $updatesFn);

		// Components: Themes
		$updatesFn = static fn ($value) => Helpers\Updates::themes()->isEnabled((bool) $value);
		$autoUpdateFn = static fn ($value) => Helpers\AutoUpdate::themes()->isEnabled((bool) $value);

		$hook->addFilter(self::defaultOptionName('auto_update_themes'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionName('update_themes'), $updatesFn);
		$hook->addFilter(self::optionName('auto_update_themes'), $autoUpdateFn);
		$hook->addFilter(self::optionName('update_themes'), $updatesFn);
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new ManageGlobal();
		yield new ManageCore();
		yield new ManagePlugins();
		yield new ManageThemes();
	}
}
