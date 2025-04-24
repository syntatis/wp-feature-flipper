<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Heartbeat;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Action;
use SSFV\Codex\Foundation\Hooks\Filter;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Admin;
use Syntatis\FeatureFlipper\Helpers\Option;

use function is_numeric;

use const PHP_INT_MAX;

final class ManageAdmin implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->parse($this);

		/**
		 * Filter the Heartbeat settings for the admin area.
		 *
		 * When Heartbeat is disabled from the global option, it should also disable
		 * the "heartbeat_admin" option.
		 */
		$hook->addFilter(
			Option::hook('heartbeat_admin'),
			static fn (mixed $value) => Option::isOn('heartbeat') ? $value : false,
		);
		$hook->addFilter(
			Option::hook('default:heartbeat_admin'),
			static fn (mixed $value) => Option::isOn('heartbeat') ? $value : false,
		);
	}

	/**
	 * Deregister the Heartbeat script in the admin area.
	 */
	#[Action(name: 'admin_init', priority: PHP_INT_MAX)]
	public function deregisterScripts(): void
	{
		if (
			! is_admin() ||
			self::isPostEditor() ||
			Option::isOn('heartbeat_admin')
		) {
			return;
		}

		wp_deregister_script('heartbeat');
	}

	/**
	 * Filter the Heartbeat settings for the admin area.
	 *
	 * @param array<string,mixed> $settings
	 *
	 * @return array<string,mixed>
	 */
	#[Filter(name: 'heartbeat_settings', priority: PHP_INT_MAX)]
	public function settings(array $settings = []): array
	{
		/**
		 * If it's not admin, return the settings as is.
		 *
		 * In the post editor, even though that it is on the admin area, settings
		 * should also return as is as well, since the settings for post editor
		 * would be applied from a separate class.
		 *
		 * Don't modify the interval when the feature is off. When is is off, the
		 * heartbeat script is deregistered, so modifying the interval is not
		 * required.
		 *
		 * @see \Syntatis\FeatureFlipper\Features\Heartbeat\ManagePostEditor
		 */
		if (
			! is_admin() ||
			! Option::isOn('heartbeat_admin') ||
			self::isPostEditor()
		) {
			return $settings;
		}

		$interval = Option::get('heartbeat_admin_interval');

		if (is_numeric($interval)) {
			$interval = absint($interval);

			$settings['interval'] = $interval;
			$settings['minimalInterval'] = $interval;
		}

		return $settings;
	}

	private static function isPostEditor(): bool
	{
		return Admin::isScreen('post.php') || Admin::isScreen('post-new.php');
	}
}
