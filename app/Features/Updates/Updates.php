<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Helpers\Option;

use function define;
use function defined;

use const PHP_INT_MAX;

final class Updates implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter(
			Option::hook('updates'),
			static fn ($value) => Helpers\Updates::global((bool) $value)->isEnabled(),
		);
		$hook->addFilter(
			Option::hook('default:auto_updates'),
			static fn ($value) => Helpers\AutoUpdate::global((bool) $value)->isEnabled(),
		);
		$hook->addFilter(
			Option::hook('auto_updates'),
			static fn ($value) => Helpers\AutoUpdate::global((bool) $value)->isEnabled(),
		);

		$hook->addAction('admin_menu', [$this, 'removeMenu'], PHP_INT_MAX);

		if (! Option::isOn('updates')) {
			$hook->removeAction('init', 'wp_schedule_update_checks');
		}

		if (! Option::isOn('auto_updates')) {
			if (! defined('AUTOMATIC_UPDATER_DISABLED')) {
				define('AUTOMATIC_UPDATER_DISABLED', false);
			}

			$hook->addFilter('auto_update_translation', '__return_false');
			$hook->addFilter('automatic_updater_disabled', '__return_false');
			$hook->removeAction('wp_maybe_auto_update', 'wp_maybe_auto_update');
			$hook->removeFilter('auto_plugin_update_send_email', '__return_false');
		}
	}

	/**
	 * Remove the "Updates" menu from the Menu in the admin area.
	 */
	public function removeMenu(): void
	{
		if (! Option::isOn('updates')) {
			remove_submenu_page('index.php', 'update-core.php');
		}
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new ManageCore();
		yield new ManagePlugins();
		yield new ManageThemes();
	}
}
