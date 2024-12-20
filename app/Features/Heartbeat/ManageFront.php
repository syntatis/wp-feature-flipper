<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Heartbeat;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use function is_int;

class ManageFront implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter('heartbeat_settings', [$this, 'getSettings']);
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
