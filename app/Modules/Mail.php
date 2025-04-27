<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use function is_string;
use function trim;

use const PHP_INT_MAX;

final class Mail implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter(
			'wp_mail_from',
			static function (string $value): string {
				$address = Option::get('mail_from_address');

				if (is_string($address) && (bool) is_email($address)) {
					return $address;
				}

				return $value;
			},
			PHP_INT_MAX,
		);

		$hook->addFilter(
			'wp_mail_from_name',
			static function (string $value): string {
				$name = Option::get('mail_from_name');

				if (is_string($name) && trim($name) !== '') {
					return $name;
				}

				return $value;
			},
			PHP_INT_MAX,
		);
	}
}
