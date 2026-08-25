<?php

declare(strict_types=1);

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
	exit;
}

// phpcs:disable Squiz.NamingConventions.ValidVariableName.NotCamelCaps

if (! function_exists('wp_get_wp_version')) {
	function wp_get_wp_version(): string
	{
		/** @var string|null $wp_version */
		static $wp_version;

		if (! isset($wp_version)) {
			require ABSPATH . WPINC . '/version.php';
		}

		return $wp_version;
	}
}
