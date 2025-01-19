=== Feature Flipper ===

Contributors: tfirdaus
Tags: disable, rss, emojis, xmlrpc, gutenberg
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.7.1
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Disable Comments, Gutenberg, Emojis, and other features you don't need.

== Description ==

This plugin gives you the ability to easily manage features in WordPress like Comments, the Block Editor (Gutenberg), Emojis, Automatic Updates, Post Embeds, XML-RPC, REST API, and more. If you don't need them, you can easily toggle them off.

It also includes some handy utilities like showing your site's environment type in the admin bar, enabling maintenance or private mode, or using random URLs for media pages.

### Features

This plugin organizes these settings into sections so you can quickly find and manage what you need:

#### General

Tweak key WordPress features to fit your needs:

* **Block Editor**: Prefer the Classic Editor? Disable the Block Editor for all or specific post types.
* **Block-based Widgets**: Turn off the Block Widgets Editor.
* **Comments**: Disable comments and remove related admin area elements.
* **Revisions**: Limit or disable post revisions to improve performance.
* **Embed**: Stop other sites from embedding your content.
* **Self-ping**: Prevent pingbacks to your own site.
* **Feeds**: Disable RSS feeds if you don't need them.

#### Admin

Customize your admin experience:

* **Dashboard Widgets**: Remove unwanted or all dashboard widgets.
* **Footer Text**: Get rid of the <q>Thank you for creating with WordPress</q> footer text.
* **Update Nags**: Hide update notices for WordPress, plugins, and themes.
* **Admin Bar**: Remove the admin bar on your site's front end.
* **Howdy Text**: Replace the <q>Howdy</q> greeting in the admin bar.
* **Environment Type**: Display site's environment type (e.g. Development, Staging, Production) in the admin bar.

#### Media

Adjust media-related settings:

* **Attachment Pages**: Disable attachment pages or re-enable them on WordPress 6.4+.
* **Attachment Slug**: Use random URLs for media pages to avoid slug conflicts.
* **Infinite Scroll**: Restore infinite scroll in the Media Library.
* **Image Quality**: Adjust the quality of uploaded images (JPEG only).

#### Site

Control access and assets on your site:

* **Access**: Make your site public, restrict it to logged-in users, or enable maintenance mode.

##### Assets

* **Emojis**: Improve performance by removing emoji scripts and styles.
* **Script Version**: Remove version query strings from scripts and styles URLs.
* **jQuery Migrate**: Disable jQuery Migrate if it’s not needed.

##### Metadata

* **RSD Link**: Remove the Really Simple Discovery (RSD) tag.
* **Generator Meta Tag**: Hide the WordPress version meta tag.
* **Shortlink**: Remove the shortlink meta tag.

#### Security

Improve your site's security by disabling or limiting access to certain features:

* **File Edit**: Disable the built-in file editor for themes and plugins.
* **XML-RPC**: Disable the XML-RPC endpoint.
* **API Authentication**: Require authentication for all REST API requests.

##### Login

Apply additional security measures to your login page:

* **Identifier**: Restrict login to username only, email only, or allow both.
* **Obfuscate Error**: Prevent hints about which login credential is incorrect.
* **Block Bots**: Block known bots from accessing the login page.

##### Passwords

* **Application Passwords**: Disable Application Passwords and remove related UI elements.

#### Advanced

Only for advanced users. You may configure these following features, but do it carefully:

* **Updates**: Enable or disable updates for WordPress, plugins, and themes.
* **Cron**: Disable WP-Cron and use a real cron job.
* **Heartbeat**: Adjust or disable the Heartbeat API to reduce resource usage.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/syntatis-feature-flipper` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings -> Flipper screen to configure the plugin.

[More info on installing plugins](https://wordpress.org/documentation/article/manage-plugins/#installing-plugins)

== Screenshots ==

1. The "General" section provides an easy way to enable and disable features in like the Block Editor, Block-based Widgets, Comments, etc.
2. The "Admin" section allows you to manage features that are in the WordPress admin area, like the Admin Bar, Dashboard Widgets, etc.
3. In "Media" section, you can manage the Media library as well as how the media upload is handled.
4. Needs to put your site in "Private Mode"? it's only a click away in the "Site" section.
5. Hardening your site? The "Security" section provides an easy way to disable some features that could be a security risk.
6. And more...

== Frequently Asked Questions ==

= Is this plugin compatible with WordPress Multisite? =

Not yet, but it's on the plan!
