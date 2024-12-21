<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Error;

use function define;
use function defined;

class Security implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('xmlrpc')) {
			$hook->addFilter('pings_open', '__return_false');
			$hook->addFilter('xmlrpc_enabled', '__return_false');
			$hook->addFilter('xmlrpc_methods', '__return_empty_array');
			$hook->removeAction('wp_head', 'rsd_link');
		}

		if (! (bool) Option::get('file_edit') && ! defined('DISALLOW_FILE_EDIT')) {
			define('DISALLOW_FILE_EDIT', true);
		}

		if (! (bool) Option::get('application_passwords')) {
			$hook->addFilter('wp_is_application_passwords_available', '__return_false');
		}

		if ((bool) Option::get('obfuscate_login_error')) {
			$hook->addFilter('login_errors', [$this, 'filterLoginErrorMessage']);
		}

		if (! (bool) Option::get('authenticated_rest_api')) {
			return;
		}

		$hook->addFilter('rest_authentication_errors', [$this, 'apiForceAuthentication']);
	}

	/**
	 * @param WP_Error|true|null $access
	 *
	 * @return WP_Error|true|null
	 */
	public function apiForceAuthentication($access)
	{
		return is_user_logged_in()
			? $access
			: new WP_Error(
				'rest_login_required',
				__('You must be logged in to access this resource.', 'syntatis-feature-flipper'),
				[
					'status' => rest_authorization_required_code(),
				],
			);
	}

	public function filterLoginErrorMessage(): string
	{
		return __('Invalid username or password.', 'syntatis-feature-flipper');
	}
}
