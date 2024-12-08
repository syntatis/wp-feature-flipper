<?php

/**
 * PHPUnit bootstrap file for WordPress plugin
 */

/**
 * Reset the composer autoloaded files which prevented the scoped functions
 * to be loaded.
 */
$GLOBALS['__composer_autoload_files'] = [];

require_once getenv( 'WP_TESTS_DIR' ) . '/includes/functions.php';

tests_add_filter(
	'muplugins_loaded',
	static fn () => require_once dirname( __DIR__, 2 ) . '/syntatis-feature-flipper.php'
);

require_once getenv( 'WP_TESTS_DIR' ) . '/includes/bootstrap.php';
