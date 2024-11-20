<?php

declare(strict_types=1);

/**
 * Plugin bootstrap file.
 *
 * This file is read by WordPress to display the plugin's information in the admin area.
 *
 * @wordpress-plugin
 * Plugin Name:       Feature Flipper
 * Plugin URI:        https://github.com/syntatis/wp-feature-flipper
 * Description:       Easily switch some features in WordPress, on and off
 * Version:           1.4.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Thoriq Firdaus
 * Author URI:        https://github.com/tfirdaus
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       syntatis-feature-flipper
 * Domain Path:       /inc/languages
 */

namespace Syntatis\FeatureFlipper;

use function defined;

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
	exit;
}

/**
 * Define the current version of the plugin.
 *
 * Following Semantic Versioning ({@link https://semver.org}) is encouraged.
 * It provides a clear understanding of the impact of changes between
 * versions.
 */
const PLUGIN_VERSION = '1.4.1';

/**
 * Define the directory path to the plugin file.
 *
 * This constant provides a convenient reference to the plugin's directory path,
 * useful for including or requiring files relative to this directory.
 */
const PLUGIN_DIR = __DIR__;

/**
 * Define the path to the plugin file.
 *
 * This path can be used in various contexts, such as managing the activation
 * and deactivation processes, loading the plugin text domain, adding action
 * links, and more.
 */
const PLUGIN_FILE = __FILE__;

/**
 * Load and initialize the WordPress plugin application.
 */
require PLUGIN_DIR . '/inc/bootstrap/app.php';
