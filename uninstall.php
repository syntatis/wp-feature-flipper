<?php

declare(strict_types=1);

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 */

// If uninstall not called from WordPress, then exit.
if (! defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

// phpcs:disable Squiz.Strings.DoubleQuoteUsage.ContainsVar
// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
$db = $GLOBALS['wpdb'] ?? null;

if (! $db instanceof wpdb) {
	return;
}

$options = $db->get_results(
	$db->prepare(
		"SELECT option_name FROM $db->options WHERE option_name LIKE %s",
		'syntatis_feature_flipper_%',
	),
);

foreach ($options as $option) {
	delete_option($option->option_name);
}
