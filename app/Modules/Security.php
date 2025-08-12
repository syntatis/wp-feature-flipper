<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\PasswordReset;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Error;

use function define;
use function defined;

final class Security implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
		if (! Option::isOn('xmlrpc')) {
			$hook->addFilter('pings_open', '__return_false');
			$hook->addFilter('xmlrpc_enabled', '__return_false');
			$hook->addFilter('xmlrpc_methods', '__return_empty_array');
			$hook->removeAction('wp_head', 'rsd_link');
		}

		if (! Option::isOn('file_edit') && ! defined('DISALLOW_FILE_EDIT')) {
			define('DISALLOW_FILE_EDIT', true);
		}

		$hook->addFilter('wp_is_application_passwords_available', static function ($available) {
			/**
			 * If the password application is enabled, return whatever value is passed
			 * to the filter. This allows other modification to the availability of
			 * the application passwords, such as from other plugins, or themes
			 * to take effect. Otherwise, always return `false`.
			 */
			return Option::isOn('application_passwords') ? $available : false;
		});
		$hook->addFilter(
			'rest_authentication_errors',
			static function ($access) {
				if (! Option::isOn('authenticated_rest_api') || is_user_logged_in()) {
					return $access;
				}

				return new WP_Error(
					'rest_login_required',
					__('Unauthorized API access.', 'syntatis-feature-flipper'),
					[
						'status' => rest_authorization_required_code(),
					],
				);
			},
		);
	}

	/** @inheritDoc */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield 'password_reset' => Option::isOn('password_reset') ? null : new PasswordReset();
	}
}
