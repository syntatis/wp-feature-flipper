<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\URL;

use function is_string;

use const PHP_INT_MAX;

final class LoginIdentifier implements Hookable
{
	private ?string $identifier;

	public function __construct()
	{
		$identifier = Option::get('login_identifier');
		$this->identifier = is_string($identifier) ? $identifier : null;
	}

	public function hook(Hook $hook): void
	{
		if ($this->identifier === 'both') {
			return;
		}

		$hook->addFilter('gettext', [$this, 'filterGetText'], PHP_INT_MAX, 3);

		switch ($this->identifier) {
			case 'email':
				$hook->removeAction('authenticate', 'wp_authenticate_username_password', 20);
				break;

			case 'username':
				$hook->removeAction('authenticate', 'wp_authenticate_email_password', 20);
				break;
		}
	}

	public function filterGetText(string $translation, string $text, string $domain): string
	{
		if (! URL::isLogin() || $domain !== 'default') {
			return $translation;
		}

		if ($text === 'Username or Email Address') {
			$identifier = $this->identifier;

			switch ($identifier) {
				case 'username':
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- Translation will be handled by Core
					return __('Username');

				case 'email':
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- Translation will be handled by Core
					return __('Email');
			}
		}

		return $translation;
	}
}
