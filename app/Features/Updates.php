<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Concerns\WPFilterNames;
use Syntatis\FeatureFlipper\Features\Updates\ManageCore;
use Syntatis\FeatureFlipper\Features\Updates\ManageGlobal;
use Syntatis\FeatureFlipper\Features\Updates\ManagePlugins;
use Syntatis\FeatureFlipper\Features\Updates\ManageThemes;
use Syntatis\FeatureFlipper\Helpers;

class Updates implements Hookable, Extendable
{
	use WPFilterNames;

	public function hook(Hook $hook): void
	{
		/**
		 * Globals
		 */
		$hook->addFilter(
			self::optionFilterName('updates'),
			static fn ($value) => Helpers\Updates::global()->isEnabled((bool) $value),
		);
		$hook->addFilter(
			self::optionFilterName('auto_updates'),
			static fn ($value) => Helpers\AutoUpdate::global()->isEnabled((bool) $value),
		);

		// Components: Core
		$updatesFn = static fn ($value) => Helpers\Updates::core()->isEnabled((bool) $value);
		$autoUpdateFn = static fn ($value) => Helpers\AutoUpdate::core()->isEnabled((bool) $value);

		$hook->addFilter(self::defaultOptionFilterName('auto_update_core'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionFilterName('update_core'), $updatesFn);
		$hook->addFilter(self::optionFilterName('auto_update_core'), $autoUpdateFn);
		$hook->addFilter(self::optionFilterName('update_core'), $updatesFn);

		// Components: Plugins
		$updatesFn = static fn ($value) => Helpers\Updates::plugins()->isEnabled((bool) $value);
		$autoUpdateFn = static fn ($value) => Helpers\AutoUpdate::plugins()->isEnabled((bool) $value);

		$hook->addFilter(self::defaultOptionFilterName('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionFilterName('update_plugins'), $updatesFn);
		$hook->addFilter(self::optionFilterName('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(self::optionFilterName('update_plugins'), $updatesFn);

		// Components: Themes
		$updatesFn = static fn ($value) => Helpers\Updates::themes()->isEnabled((bool) $value);
		$autoUpdateFn = static fn ($value) => Helpers\AutoUpdate::themes()->isEnabled((bool) $value);

		$hook->addFilter(self::defaultOptionFilterName('auto_update_themes'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionFilterName('update_themes'), $updatesFn);
		$hook->addFilter(self::optionFilterName('auto_update_themes'), $autoUpdateFn);
		$hook->addFilter(self::optionFilterName('update_themes'), $updatesFn);
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
