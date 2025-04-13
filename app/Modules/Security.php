<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
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

		if (! Option::isOn('application_passwords')) {
			$hook->addFilter('wp_is_application_passwords_available', '__return_false');
		}

		if (Option::isOn('obfuscate_login_error')) {
			$hook->addFilter('login_errors', [$this, 'filterLoginErrorMessage']);
		}

		if (Option::isOn('login_block_bots')) {
			$hook->addAction('wp', [$this, 'blockBots'], PHP_INT_MIN);
		}

		if (! Option::isOn('authenticated_rest_api')) {
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
				__('Unauthorized API access.', 'syntatis-feature-flipper'),
				[
					'status' => rest_authorization_required_code(),
				],
			);
	}

	public function filterLoginErrorMessage(): string
	{
		return __(
			'<strong>Error:</strong> Login failed. Please ensure your credentials are correct.',
			'syntatis-feature-flipper',
		);
	}

	public function blockBots(): void
	{
		if (! URL::isLogin()) {
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
