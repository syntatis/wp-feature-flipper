<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Filter;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\URL;

use function is_string;

final class LoginIdentifier implements Hookable
{
	private string|null $identifier;

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

		$hook->parse($this);

		switch ($this->identifier) {
			case 'email':
				$hook->removeAction('authenticate', 'wp_authenticate_username_password', 20);
				break;

			case 'username':
				$hook->removeAction('authenticate', 'wp_authenticate_email_password', 20);
				break;
		}
	}

	#[Filter('gettext', priority: 20, acceptedArgs: 3)]
	public function getText(string $translation, string $text, string $domain): string
	{
		if (! URL::isLogin() || $domain !== 'default') {
			return $translation;
		}

		if ($text === 'Username or Email Address') {
			$identifier = $this->identifier;

			return match ($identifier) {
				'username' => __('Username'), // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- Translation will be handled by Core
				'email' => __('Email'), // phpcs:ignore WordPress.WP.I18n.MissingArgDomain -- Translation will be handled by Core
				default => $translation,
			};
		}

		return $translation;
	}
}
