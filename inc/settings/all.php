<?php

declare(strict_types=1);

// If this file is called directly, abort.
if (! defined('ABSPATH')) {
	exit;
}

use SSFV\Codex\Settings\Setting;

/**
 * Defines the options to be used by the plugin. Aside the name and type,
 * each option may also define some constraints and default. Feel free
 * modify it to suit your needs.
 */
return [
	// General.
	(new Setting('gutenberg', 'boolean'))
		->withDefault(true),
	(new Setting('block_based_widgets', 'boolean'))
		/**
		 * Since it's too early to determine whether the current theme supports the
		 * block-based widgets, set the default to `null`, and patch it through
		 * the filter.
		 *
		 * @see syntatis/feature_flipper/settings The filter to patch the setting values.
		 * @see \Syntatis\FeatureFlipper\Switches\General The class that patches the `block_based_widgets` value.
		 */
		->withDefault(null),
	(new Setting('revisions', 'boolean'))
		->withDefault(defined('WP_POST_REVISIONS') ? ! (bool) WP_POST_REVISIONS : true),
	(new Setting('revisions_max', 'integer'))
		->withDefault(null),
	(new Setting('self_ping', 'boolean'))
		->withDefault(true),
	(new Setting('cron', 'boolean'))
		->withDefault(true),
	(new Setting('embed', 'boolean'))
		->withDefault(true),
	(new Setting('comments', 'boolean'))
		->withDefault(true),
	(new Setting('feeds', 'boolean'))
		->withDefault(true),

	// Admin.
	(new Setting('dashboard_widgets', 'boolean'))
		->withDefault(true),
	(new Setting('dashboard_widgets_enabled', 'array'))
		->apiSchema(['items' => ['type' => 'string']])
		->withDefault(null),
	(new Setting('admin_footer_text', 'boolean'))
		->withDefault(true),
	(new Setting('update_nags', 'boolean'))
		->withDefault(true),

	// Admin: Admin Bar.
	(new Setting('admin_bar', 'boolean'))
		->withDefault(true),
	(new Setting('admin_bar_menu', 'array'))
		->apiSchema(['items' => ['type' => 'string']])
		->withDefault(null),
	(new Setting('admin_bar_howdy', 'boolean'))
		->withDefault(true),
	(new Setting('admin_bar_env_type', 'boolean'))
		->withDefault(false),

	// Media.
	(new Setting('attachment_page', 'boolean'))
		->withDefault(true),
	(new Setting('attachment_slug', 'boolean'))
		->withDefault(true),
	(new Setting('media_infinite_scroll', 'boolean'))
		->withDefault(false),
	(new Setting('jpeg_compression', 'boolean'))
		->withDefault(true),

	/**
	 * @see https://developer.wordpress.org/reference/hooks/jpeg_quality/
	 * @see https://github.com/WordPress/wordpress-develop/blob/trunk/src/wp-includes/class-wp-image-editor.php#L24 - Default value.
	 */
	(new Setting('jpeg_compression_quality', 'integer'))
		->withDefault(82),

	// Site.
	(new Setting('site_private', 'boolean'))
		->withDefault(false),

	// Site: Assets.
	(new Setting('emojis', 'boolean'))
		->withDefault(true),
	(new Setting('scripts_version', 'boolean'))
		->withDefault(true),
	(new Setting('jquery_migrate', 'boolean'))
		->withDefault(true),

	// Site: Metadata.
	(new Setting('rsd_link', 'boolean'))
		->withDefault(true),
	(new Setting('generator_tag', 'boolean'))
		->withDefault(true),
	(new Setting('shortlink', 'boolean'))
		->withDefault(true),

	// Security.
	(new Setting('xmlrpc', 'boolean'))
		->withDefault(true),
	(new Setting('file_edit', 'boolean'))
		->withDefault(true),
	(new Setting('authenticated_rest_api', 'boolean'))
		->withDefault(false),

	/**
	 * ========================================================
	 * Advanced
	 * ========================================================
	 */

	/**
	 * Updates
	 */
	(new Setting('updates', 'boolean'))
		->withDefault(true),
	(new Setting('update_core', 'boolean'))
		->withDefault(true),
	(new Setting('update_plugins', 'boolean'))
		->withDefault(true),
	(new Setting('update_themes', 'boolean'))
		->withDefault(true),

	/**
	 * Updates: Auto Updates
	 */
	(new Setting('auto_updates', 'boolean'))
		->withDefault(true),
	(new Setting('auto_update_core', 'boolean'))
		->withDefault(true),
	(new Setting('auto_update_plugins', 'boolean'))
		->withDefault(true),
	(new Setting('auto_update_themes', 'boolean'))
		->withDefault(true),

	/**
	 * Heartbeat
	 */
	(new Setting('heartbeat', 'boolean'))
		->withDefault(true),
	(new Setting('heartbeat_admin', 'boolean'))
		->withDefault(true),
	(new Setting('heartbeat_admin_interval', 'integer'))
		->withDefault(60),
	(new Setting('heartbeat_post_editor', 'boolean'))
		->withDefault(true),
	(new Setting('heartbeat_post_editor_interval', 'integer'))
		->withDefault(15),
];
