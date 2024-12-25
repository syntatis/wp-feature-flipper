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
	/**
	 * --------------------------------------------------------
	 * General
	 * --------------------------------------------------------
	 *
	 * Defines the options to manage features grouped in the General section.
	 *
	 * Features in the General section are those that are not specific to
	 * any particular area of the WordPress site. They also generally
	 * are safe to be disabled.
	 *
	 * @see \Syntatis\FeatureFlipper\Modules\General
	 */
	(new Setting('gutenberg', 'boolean'))
		->withDefault(true),
	(new Setting('gutenberg_post_types', 'array'))
		->apiSchema(['items' => ['type' => 'string']])
		/**
		 * Since it is too early to determine all the post types registered on the
		 * site, the default value will be applied through the plugin filter.
		 *
		 * @see https://developer.wordpress.org/reference/hooks/default_option_option/
		 */
		->withDefault(null),
	(new Setting('block_based_widgets', 'boolean'))
		/**
		 * Since it's too early to determine whether the current theme supports the
		 * block-based widgets, set the default to `null`, and patch it through
		 * the filter.
		 *
		 * @see https://developer.wordpress.org/reference/hooks/default_option_option/
		 */
		->withDefault(null),
	(new Setting('revisions', 'boolean'))
		->withDefault(defined('WP_POST_REVISIONS') ? (bool) WP_POST_REVISIONS : true),
	(new Setting('revisions_max', 'integer'))
		/**
		 * The default value will be set to the value of the `WP_POST_REVISIONS`.
		 *
		 * @see https://wordpress.org/documentation/article/revisions/#revision-options
		 */
		->withDefault(defined('WP_POST_REVISIONS') && is_numeric(WP_POST_REVISIONS) ? (int) WP_POST_REVISIONS : null),
	(new Setting('self_ping', 'boolean'))
		->withDefault(true),
	(new Setting('embed', 'boolean'))
		->withDefault(true),
	(new Setting('comments', 'boolean'))
		->withDefault(true),
	(new Setting('feeds', 'boolean'))
		->withDefault(true),

	/**
	 * --------------------------------------------------------
	 * Admin
	 * --------------------------------------------------------
	 *
	 * Defines the options to manage features grouped in the Admin section.
	 *
	 * Features in the Admin section are those that are specific to the
	 * WordPress admin area. Users won't see the changes unless they
	 * have access to the admin area.
	 *
	 * @see \Syntatis\FeatureFlipper\Modules\Admin
	 */
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

	/**
	 * --------------------------------------------------------
	 * Media
	 * --------------------------------------------------------
	 *
	 * Defines the options to manage the WordPress Media features.
	 *
	 * These features generally will affect the Media library screen, and how
	 * WordPress will handle the uploaded media files.
	 *
	 * @see \Syntatis\FeatureFlipper\Modules\Media
	 */
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

	/**
	 * --------------------------------------------------------
	 * Site
	 * --------------------------------------------------------
	 *
	 * Defines the options to manage features affecting the site, in general.
	 *
	 * Features in this section will manage how the site will handle scripts, styles,
	 * content, and anything in between that's rendered on the site page.
	 *
	 * @see \Syntatis\FeatureFlipper\Modules\Site
	 */
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

	/**
	 * --------------------------------------------------------
	 * Security
	 * --------------------------------------------------------
	 *
	 * @see \Syntatis\FeatureFlipper\Modules\Security
	 */
	(new Setting('xmlrpc', 'boolean'))
		->withDefault(true),
	(new Setting('file_edit', 'boolean'))
		->withDefault(true),
	(new Setting('authenticated_rest_api', 'boolean'))
		->withDefault(false),

	// Security: Login.
	(new Setting('login_identifier', 'string'))
		->withDefault('both'),
	(new Setting('obfuscate_login_error', 'boolean'))
		->withDefault(false),
	(new Setting('login_block_bots', 'boolean'))
		->withDefault(false),

	// Security: Passwords.
	(new Setting('application_passwords', 'boolean'))
		->withDefault(true),
	/**
	 * --------------------------------------------------------
	 * Security
	 * --------------------------------------------------------
	 *
	 * @see \Syntatis\FeatureFlipper\Modules\Advanced
	 */
	(new Setting('cron', 'boolean'))
		->withDefault(true),

	// Updates.
	(new Setting('updates', 'boolean'))
		->withDefault(true),
	(new Setting('update_core', 'boolean'))
		->withDefault(true),
	(new Setting('update_plugins', 'boolean'))
		->withDefault(true),
	(new Setting('update_themes', 'boolean'))
		->withDefault(true),

	// Updates: Auto Updates
	(new Setting('auto_updates', 'boolean'))
		->withDefault(true),
	(new Setting('auto_update_core', 'boolean'))
		->withDefault(true),
	(new Setting('auto_update_plugins', 'boolean'))
		->withDefault(true),
	(new Setting('auto_update_themes', 'boolean'))
		->withDefault(true),

	// Heartbeat.
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
