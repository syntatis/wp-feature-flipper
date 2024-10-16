<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex;

use function defined;
use function file_exists;

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
	exit;
}

/**
 * Load dependencies using the Composer autoloader.
 *
 * This allows us to load third-party libraries without having to include
 * or require the files from the libraries manually.
 *
 * This file is generated by PHP-Scoper, a Composer package to scopes the
 * namespaces of third-party libraries. It prevents conflict with other
 * plugins or themes that might use the same libraries with the same
 * namespaces or class names.
 *
 * @see https://getcomposer.org/doc/01-basic-usage.md#autoloading
 * @see https://deliciousbrains.com/php-scoper-namespace-composer-dependencies/
 * @see https://github.com/humbug/php-scoper
 */
require PLUGIN_DIR . '/dist/autoload/vendor/scoper-autoload.php';

/**
 * Load the environment-specific application runtime.
 */
$appEnv = PLUGIN_DIR . '/inc/bootstrap/app.' . wp_get_environment_type() . '.php';

if (file_exists($appEnv)) {
	require $appEnv;
}

/**
 * Initialize the plugin application.
 */
(new Codex\Plugin(new Plugin()))
	->setPluginFilePath(PLUGIN_FILE)
	->addServices(include PLUGIN_DIR . '/inc/bootstrap/providers.php')
	->boot();
