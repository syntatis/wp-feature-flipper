<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use function is_string;
use function sprintf;
use function trim;

trait WithURI
{
	private static function getCurrentUrl(): string
	{
		$schema = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
		$host = isset($_SERVER['HTTP_HOST']) && is_string($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		$uri = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

		return trim($host) !== '' ? sprintf('%s%s%s', $schema, $host, $uri) : '';
	}
}
