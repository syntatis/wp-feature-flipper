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
	(new Setting('heartbeat', 'boolean'))
		->withDefault(true),
	(new Setting('self_ping', 'boolean'))
		->withDefault(true),
	(new Setting('cron', 'boolean'))
		->withDefault(true),
	(new Setting('embed', 'boolean'))
		->withDefault(true),
	(new Setting('auto_update', 'boolean'))
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

	// Media.
	(new Setting('attachment_page', 'boolean'))
		->withDefault(true),
	(new Setting('attachment_slug', 'boolean'))
		->withDefault(true),
	(new Setting('jpeg_compression', 'boolean'))
		->withDefault(true),
	/** @see https://developer.wordpress.org/reference/hooks/jpeg_quality/ */
	(new Setting('jpeg_compression_quality', 'integer'))
		->withDefault(apply_filters('jpeg_quality', 82)),

	// Assets.
	(new Setting('emojis', 'boolean'))
		->withDefault(true),
	(new Setting('scripts_version', 'boolean'))
		->withDefault(true),
	(new Setting('jquery_migrate', 'boolean'))
		->withDefault(true),

	// Webpage.
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
];
