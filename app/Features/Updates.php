<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Features\Updates\Helpers;
use Syntatis\FeatureFlipper\Features\Updates\ManageCore;
use Syntatis\FeatureFlipper\Features\Updates\ManagePlugins;
use Syntatis\FeatureFlipper\Features\Updates\ManageThemes;
use Syntatis\FeatureFlipper\Helpers\Option;

use function define;
use function defined;

use const PHP_INT_MAX;

class Updates implements Hookable, Extendable
{
	use WithHookName;

	public function hook(Hook $hook): void
	{
		$hook->addFilter(
			self::optionHook('updates'),
			static fn ($value) => Helpers\Updates::global((bool) $value)->isEnabled(),
		);
		$hook->addFilter(
			self::defaultOptionHook('auto_updates'),
			static fn ($value) => Helpers\AutoUpdate::global((bool) $value)->isEnabled(),
		);
		$hook->addFilter(
			self::optionHook('auto_updates'),
			static fn ($value) => Helpers\AutoUpdate::global((bool) $value)->isEnabled(),
		);

		if (! Option::isOn('updates')) {
			$hook->addAction('admin_menu', [$this, 'removeMenu'], PHP_INT_MAX);
			$hook->removeAction('init', 'wp_schedule_update_checks');
		}

		if (Option::isOn('auto_updates')) {
			return;
		}

		if (! defined('AUTOMATIC_UPDATER_DISABLED')) {
			define('AUTOMATIC_UPDATER_DISABLED', false);
		}

		$hook->addFilter('auto_update_translation', '__return_false');
		$hook->addFilter('automatic_updater_disabled', '__return_false');
		$hook->removeAction('wp_maybe_auto_update', 'wp_maybe_auto_update');
	}

	/**
	 * Remove the "Updates" menu from the Menu in the admin area.
	 */
	public function removeMenu(): void
	{
		remove_submenu_page('index.php', 'update-core.php');
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new ManageCore();
		yield new ManagePlugins();
		yield new ManageThemes();
	}
}
