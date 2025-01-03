<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Heartbeat;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Concerns\WithPostEditor;
use Syntatis\FeatureFlipper\Helpers\Option;

use function is_numeric;

use const PHP_INT_MAX;

class ManageAdmin implements Hookable
{
	use WithHookName;
	use WithPostEditor;

	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_init', [$this, 'deregisterScripts'], PHP_INT_MAX);
		$hook->addFilter('heartbeat_settings', [$this, 'filterSettings'], PHP_INT_MAX);

		/**
		 * Filter the Heartbeat settings for the admin area.
		 *
		 * When Heartbeat is disabled from the global option, it should also disable
		 * the "heartbeat_admin" option.
		 */
		$hook->addFilter(
			self::optionHook('heartbeat_admin'),
			static fn ($value) => Option::isOn('heartbeat') ? $value : false,
		);
		$hook->addFilter(
			self::defaultOptionHook('heartbeat_admin'),
			static fn ($value) => Option::isOn('heartbeat') ? $value : false,
		);
	}

	/**
	 * Deregister the Heartbeat script in the admin area.
	 */
	public function deregisterScripts(): void
	{
		if (! is_admin() || self::isPostEditor() || Option::isOn('heartbeat_admin')) {
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
	public function filterSettings(array $settings = []): array
	{
		/**
		 * If it's not admin, return the settings as is.
		 *
		 * In the post editor, even though that it is on the admin area, settings
		 * should also return as is as well, since the settings for post editor
		 * would be applied from a separate class.
		 *
		 * @see \Syntatis\FeatureFlipper\Features\Heartbeat\ManagePostEditor
		 */
		if (! is_admin() || self::isPostEditor() || Option::isOn('heartbeat_admin')) {
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
}
