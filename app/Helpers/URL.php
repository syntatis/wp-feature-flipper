<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use function is_string;
use function parse_url;
use function rtrim;
use function sprintf;
use function stripos;
use function trim;

use const PHP_URL_PATH;

/**
 * General methods to work with URIs and URLs.
 */
class URL
{
	private function __construct()
	{
	}

	/**
	 * Retrieve the current request URL.
	 */
	public static function getCurrent(): string
	{
		$schema = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
		$host = isset($_SERVER['HTTP_HOST']) && is_string($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		$uri = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

		return trim($host) !== '' ? sprintf('%s%s%s', $schema, $host, $uri) : '';
	}

	/**
	 * Check if the current request is the WordPress login page.
	 */
	public static function isLogin(): bool
	{
		$urlLogin = wp_login_url();
		$scriptName = isset($_SERVER['SCRIPT_NAME']) && is_string($_SERVER['SCRIPT_NAME']) ?
			$_SERVER['SCRIPT_NAME'] :
			'';

		/**
		 * Logic derived from the `is_login` function, which is only available in
		 * WordPress 6.1 or later.
		 *
		 * @see https://github.com/WordPress/WordPress/blob/master/wp-includes/load.php#L1307-L1309
		 */
		if (stripos($urlLogin, $scriptName) !== false) {
			return true;
		}

		// Try to identify if the login page is customized.
		$url = self::getCurrent();
		$urlPath = rtrim((string) parse_url($url, PHP_URL_PATH), '/');

		return rtrim((string) parse_url($urlLogin, PHP_URL_PATH), '/') === $urlPath;
	}
}
