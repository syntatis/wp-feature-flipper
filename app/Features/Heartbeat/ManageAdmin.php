<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Heartbeat;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\HasHookName;
use Syntatis\FeatureFlipper\Helpers\Option;

use function is_int;

use const PHP_INT_MAX;

class ManageAdmin implements Hookable
{
	use HasHookName;

	private bool $heartbeat;

	public function __construct()
	{
		$this->heartbeat = (bool) Option::get('heartbeat');
	}

	public function hook(Hook $hook): void
	{
		$hook->addFilter(
			'heartbeat_settings',
			[$this, 'getSettings'],
			/**
			 * Let the `heartbeat_settings` filter on `ManagePostEditor` class to run
			 * last to ensure that the heartbeat settings are correctly applied on
			 * the post editor.
			 */
			PHP_INT_MAX - 10,
		);
		$hook->addFilter(
			self::optionName('heartbeat_admin'),
			fn ($value) => $this->heartbeat ? $value : false,
		);
		$hook->addFilter(
			self::defaultOptionName('heartbeat_admin'),
			fn ($value) => $this->heartbeat ? $value : false,
		);
		$hook->addFilter(
			self::optionName('heartbeat_admin_interval'),
			fn ($value) => (bool) Option::get('heartbeat_admin') && $this->heartbeat ? $value : null,
		);
		$hook->addFilter(
			self::defaultOptionName('heartbeat_admin_interval'),
			fn ($value) => (bool) Option::get('heartbeat_admin') && $this->heartbeat ? $value : null,
		);
	}

	/**
	 * @param array<string,mixed> $settings
	 *
	 * @return array<string,mixed>
	 */
	public function getSettings(array $settings): array
	{
		if (! is_admin()) {
			return $settings;
		}

		$interval = Option::get('heartbeat_admin_interval');

		if (is_int($interval)) {
			$settings['minimalInterval'] = $interval;
		}

		return $settings;
	}
}
