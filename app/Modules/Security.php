<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Action;
use SSFV\Codex\Foundation\Hooks\Filter;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Jaybizzle\CrawlerDetect\CrawlerDetect;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\LoginIdentifier;
use Syntatis\FeatureFlipper\Features\ObfuscateUsernames;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\URL;
use WP_Error;

use function define;
use function defined;

use const PHP_INT_MIN;

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

		if (Option::isOn('application_passwords')) {
			return;
		}

		$hook->addFilter('wp_is_application_passwords_available', '__return_false');
	}

	#[Filter('rest_authentication_errors')]
	public function apiForceAuthentication(WP_Error|bool|null $access): WP_Error|bool|null
	{
		if (! Option::isOn('authenticated_rest_api')) {
			return $access;
		}

		return is_user_logged_in()
			? $access
			: new WP_Error(
				'rest_login_required',
				__('Unauthorized API access.', 'syntatis-feature-flipper'),
				[
					'status' => rest_authorization_required_code(),
				],
			);
	}

	#[Filter('login_errors')]
	public function addLoginErrorMessage(string $errorMessage): string
	{
		if (! Option::isOn('obfuscate_login_error')) {
			return $errorMessage;
		}

		return __(
			'<strong>Error:</strong> Login failed. Please ensure your credentials are correct.',
			'syntatis-feature-flipper',
		);
	}

	#[Action('wp', priority: PHP_INT_MIN)]
	public function blockBots(): void
	{
		/**
		 * If the login_block_bots option is not enabled, or if the current URL is
		 * not a login page, return early to prevent unnecessary processing for
		 * non-login requests.
		 */
		if (! Option::isOn('login_block_bots') || ! URL::isLogin()) {
			return;
		}

		$crawlerDetect = new CrawlerDetect();

		if (! $crawlerDetect->isCrawler()) {
			return;
		}

		wp_die(
			esc_html(__('You are not allowed to access this page.', 'syntatis-feature-flipper')),
			esc_html(__('Forbidden', 'syntatis-feature-flipper')),
			403,
		);
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new LoginIdentifier();
		yield new ObfuscateUsernames();
	}
}
