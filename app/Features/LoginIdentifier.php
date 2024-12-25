<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use const PHP_INT_MAX;

class LoginIdentifier implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter('gettext', [$this, 'filterGetText'], PHP_INT_MAX, 3);
	}

	public function filterGetText(string $translation, string $text, string $domain): string
	{
		if (is_login() && $domain === 'default' && $text === 'Username or Email Address') {
			$identifier = Option::get('login_identifier');

			switch ($identifier) {
				case 'username':
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- Translation will be handled by Core
					return __('Username');

				case 'email':
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- Translation will be handled by Core
					return __('Email Address');
			}
		}

		return $translation;
	}
}
