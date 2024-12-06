<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Helpers;
use Syntatis\FeatureFlipper\Option;

class Updates implements Hookable, Extendable
{
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
			self::optionFilterName('auto_update'),
			static fn ($value) => Helpers\AutoUpdate::global()->isEnabled((bool) $value),
		);

		// Components: Core
		$updatesFn = static fn ($value) => Helpers\Updates::core()->isEnabled((bool) $value);
		$autoUpdateFn = static fn ($value) => Helpers\AutoUpdate::core()->isEnabled((bool) $value);

		$hook->addFilter(self::defaultOptionFilterName('auto_update_core'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionFilterName('updates_core'), $updatesFn);
		$hook->addFilter(self::optionFilterName('auto_update_core'), $autoUpdateFn);
		$hook->addFilter(self::optionFilterName('updates_core'), $updatesFn);

		// Components: Plugins
		$updatesFn = static fn ($value) => Helpers\Updates::plugins()->isEnabled((bool) $value);
		$autoUpdateFn = static fn ($value) => Helpers\AutoUpdate::plugins()->isEnabled((bool) $value);

		$hook->addFilter(self::defaultOptionFilterName('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionFilterName('updates_plugins'), $updatesFn);
		$hook->addFilter(self::optionFilterName('auto_update_plugins'), $autoUpdateFn);
		$hook->addFilter(self::optionFilterName('updates_plugins'), $updatesFn);

		// Components: Themes
		$updatesFn = static fn ($value) => Helpers\Updates::themes()->isEnabled((bool) $value);
		$autoUpdateFn = static fn ($value) => Helpers\AutoUpdate::themes()->isEnabled((bool) $value);

		$hook->addFilter(self::defaultOptionFilterName('auto_update_themes'), $autoUpdateFn);
		$hook->addFilter(self::defaultOptionFilterName('updates_themes'), $updatesFn);
		$hook->addFilter(self::optionFilterName('auto_update_themes'), $autoUpdateFn);
		$hook->addFilter(self::optionFilterName('updates_themes'), $updatesFn);
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new Updates\ManageCore();
	}

	/** @phpstan-param non-empty-string $name */
	private static function optionFilterName(string $name): string
	{
		return 'option_' . Option::name($name);
	}

	/** @phpstan-param non-empty-string $name */
	private static function defaultOptionFilterName(string $name): string
	{
		return 'default_option_' . Option::name($name);
	}
}
