<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use function is_string;
use function parse_url;
use function rtrim;
use function sprintf;
use function trim;

use const PHP_URL_PATH;

/**
 * General methods to work with URIs.
 */
trait WithURI
{
	/**
	 * Retrieve the current request URL.
	 */
	private static function getCurrentURL(): string
	{
		$schema = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
		$host = isset($_SERVER['HTTP_HOST']) && is_string($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		$uri = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

		return trim($host) !== '' ? sprintf('%s%s%s', $schema, $host, $uri) : '';
	}

	/**
	 * Check if the current request is the WordPress login page.
	 */
	private static function isLoginURL(): bool
	{
		if (is_login()) {
			return true;
		}

		// Try to identify if the login page is customized.
		$url = self::getCurrentURL();
		$urlPath = rtrim((string) parse_url($url, PHP_URL_PATH), '/');

		return rtrim((string) parse_url(wp_login_url(), PHP_URL_PATH), '/') === $urlPath;
	}
}
