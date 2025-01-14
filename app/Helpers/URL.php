<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use Syntatis\FeatureFlipper\Concerns\DontInstantiate;

use function is_string;
use function parse_url;
use function rtrim;
use function sprintf;
use function stripos;
use function trim;

use const PHP_URL_PATH;

/**
 * General methods to work with URLs.
 */
final class URL
{
	use DontInstantiate;

	/**
	 * Retrieve URL of the current request.
	 */
	public static function current(): string
	{
		$schema = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
		$host = isset($_SERVER['HTTP_HOST']) && is_string($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		$uri = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

		return trim($host) !== '' ? sprintf('%s%s%s', $schema, $host, $uri) : '';
	}

	/**
	 * Determines whether the current request is for the login screen.
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
		$urlPath = rtrim((string) parse_url(self::current(), PHP_URL_PATH), '/');

		return rtrim((string) parse_url($urlLogin, PHP_URL_PATH), '/') === $urlPath;
	}
}
