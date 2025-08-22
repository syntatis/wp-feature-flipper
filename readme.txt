=== Feature Flipper ===

Contributors: tfirdaus
Tags: tweaks, comments, updates, admin, security
Requires at least: 6.4
Tested up to: 6.8
Stable tag: 2.0.0
Requires PHP: 7.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Disable Comments, Gutenberg, Emojis, and other features you don't need in WordPress

== Description ==

This plugin gives you the ability to manage core WordPress features like Comments, the Block Editor (Gutenberg), Emojis, XML-RPC, Feeds, Updates, Automatic Updates, Cron, Heartbeat, and more. If you don't need certain features, you can easily toggle them off or customize their behavior.

It also includes additional utility features, like showing your site's environment type in the admin bar, enabling maintenance or private mode, or using random URLs for media pages, which you can enable or disable as needed.

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

Unload some of the assets that are not always necessary for your site:

* **Emojis**: Improve performance by removing emoji scripts and styles.
* **Script Version**: Remove version query strings from scripts and styles URLs.
* **jQuery Migrate**: Disable jQuery Migrate if it’s not needed.

Clean-up some unused metadata from your site:

* **RSD Link**: Remove the Really Simple Discovery (RSD) tag.
* **Generator Meta Tag**: Remove the WordPress version meta tag.
* **Shortlink**: Remove the shortlink meta tag.

#### Security

Improve your site's security by disabling or limiting access to certain features:

* **File Edit**: Disable the built-in file editor for themes and plugins.
* **XML-RPC**: Disable the XML-RPC endpoint.
* **API Authentication**: Require authentication for all REST API requests.

Apply additional security measures to your login page:

* **Identifier**: Restrict login to username only, email only, or allow both.
* **Obfuscate Error**: Prevent hints about which login credential is incorrect.
* **Block Bots**: Block known bots from accessing the login page.
* **Obfuscate Usernames**: Expose randomize slug for users.

Manage passwords policy on the site:

* **Application Passwords**: Disable Application Passwords and remove related UI elements.

#### Advanced

For advanced users, you may configure these following features, but do it carefully:

* **Updates**: Enable or disable updates for WordPress, plugins, and themes.
* **Cron**: Disable WP-Cron and use a real cron job.
* **Heartbeat**: Adjust or disable the Heartbeat API to reduce resource usage.

== Installation ==

= Installation from within WordPress =

1. Visit **Plugins › Add New**.
2. Search for **Feature Flipper**.
3. Install and activate the **Feature Flipper** plugin.

= Manual upload =

1. Upload the entire `syntatis-feature-flipper` folder to the `/wp-content/plugins/` directory.
2. Visit **Plugins**.
3. Activate the **Feature Flipper** plugin.

Learn more [about installing plugins](https://wordpress.org/documentation/article/manage-plugins/#installing-plugins).

== Frequently Asked Questions ==

= Is this plugin compatible with WordPress Multisite? =

Not yet, but it's on the plan!

= How can I report security bugs? =

You can report security bugs through the Patchstack Vulnerability Disclosure Program. The Patchstack team help validate, triage and handle any security vulnerabilities. [Report a security vulnerability](https://patchstack.com/database/wordpress/plugin/syntatis-feature-flipper/vdp).

== Screenshots ==

1. The "General" section provides an easy way to enable and disable core features in like the Block Editor, Block-based Widgets, Comments, etc.
2. The "Admin" section allows you to manage features that are in the admin area, like the Admin Bar, Dashboard Widgets, etc.
3. In "Media" section, you can manage the Media Library as well as how the media upload is handled.
4. Needs to put your site in "Maintenance Mode"? it's only a click away in the "Site" section.
5. Hardening your site? The "Security" section provides an easy way to disable some features that could be a security risk.
6. And more...

== Changelog ==

= 1.9.5 =

* Improve setting main labels.
* Make some minor improvements on handling default values.

= 1.9.4 =

* Fix missing translatable strings.
* Fix translation loaded too early.

= 1.9.3 =

* Fix inconsistencies in some translatable strings.
* Fix menu options on the Admin Bar setting.

= 1.9.2 =

* Add "Help" tab in the setting page.
* Refine some translatable strings.

= 1.9.1 =

* Add support to identify the WooCommerce "My Account" page as a login page.

= 1.9.0 =

* Add option to obfuscate usernames.
* Add an uninstaller to remove the plugin option when the plugin is uninstalled.
