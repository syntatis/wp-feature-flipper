<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Heartbeat;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\HasHookName;
use Syntatis\FeatureFlipper\Helpers\Option;

use function is_int;

use const PHP_INT_MAX;

class ManageFront implements Hookable
{
	use HasHookName;

	private bool $heartbeat;

	public function __construct()
	{
		$this->heartbeat = (bool) Option::get('heartbeat');
	}

	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_init', [$this, 'deregisterScripts'], PHP_INT_MAX);
		$hook->addFilter('heartbeat_settings', [$this, 'getSettings'], PHP_INT_MAX);
		$hook->addFilter(
			self::optionName('heartbeat_front'),
			fn ($value) => $this->heartbeat ? $value : false,
		);
		$hook->addFilter(
			self::defaultOptionName('heartbeat_front'),
			fn ($value) => $this->heartbeat ? $value : false,
		);
		$hook->addFilter(
			self::optionName('heartbeat_front_interval'),
			fn ($value) => (bool) Option::get('heartbeat_front') && $this->heartbeat ? $value : null,
		);
		$hook->addFilter(
			self::defaultOptionName('heartbeat_front_interval'),
			fn ($value) => (bool) Option::get('heartbeat_front') && $this->heartbeat ? $value : null,
		);
	}

	public function deregisterScripts(): void
	{
		if (is_admin() || (bool) Option::get('heartbeat_post_front')) {
			return;
		}

		wp_deregister_script('heartbeat');
	}

	/**
	 * @param array<string,mixed> $settings
	 *
	 * @return array<string,mixed>
	 */
	public function getSettings(array $settings): array
	{
		if (is_admin()) {
			return $settings;
		}

		$interval = Option::get('heartbeat_front_interval');

		if (is_int($interval)) {
			$settings['minimalInterval'] = Option::get('heartbeat_front_interval');
		}

		return $settings;
	}
}
