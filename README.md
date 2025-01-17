# 🚥 Feature Flipper

![Banner](.wporg/banner-1544x500.png)

[![ci](https://github.com/syntatis/wp-feature-flipper/actions/workflows/ci.yml/badge.svg)](https://github.com/syntatis/wp-feature-flipper/actions/workflows/ci.yml) ![WordPress Plugin: Tested WP Version](https://img.shields.io/wordpress/plugin/tested/syntatis-feature-flipper) ![WordPress Plugin: Required WP Version](https://img.shields.io/wordpress/plugin/wp-version/syntatis-feature-flipper) ![WordPress Plugin Required PHP Version](https://img.shields.io/wordpress/plugin/required-php/syntatis-feature-flipper) 

> [!NOTE]  
> This plugin serves as a showcase for [Howdy](https://github.com/syntatis/howdy), a starter kit for develping a WordPress® plugin that encourages modern development practices, and provides pre-configured tools for a more streamlined development experience.

WordPress® has many features, some of which are useful and essential. However, there are likely some you don't need.

This plugin makes it easy to turn on or off these features such as Comments, the Block Editor (Gutenberg), Emojis, Automatic Updates, Post Embeds, XML-RPC, and REST API. It also comes with some additional utility features such as to show the environment type of your site in the admin bar, to enable private or maintenance mode, or to use random URL for Media pages.

## Features

This plugin manage a number of features and put them into several different sections on the setting page to make it more manageable to find the feature that you want to toggle on and off:

### General

In this section, you can find some of core features in WordPress® that you may commonly want to turn on or off, or reconfigure to your suit your needs.

* **Block Editor**: Not a fan of the Block Editor? You can turn it off easily for all or select post types and use the Classic Editor instead.
* **Block-based Widgets**: You can also disable the Block-based Widgets Editor.
* **Comments**: Turn off comments, and remove its related interface elements from the admin area.
* **Revisions**: Improve the performance of your site by limiting the number of revisions for each post, or turn it off completely.
* **Embed**: Prevent other sites to embed your content.
* **Self-ping**: Prevent WordPress® to send pingbacks to your own site.
* **Feeds**: Disable Feeds no your site that provides a way for your visitors to subscribe to your content without visiting your site.

### Admin

In the Admin section, you can find features that are shown or run in the admin area of your site.

* **Dashboard Widgets**: Clean-up your dashboard by removing some of the default widgets, or remove them all.
* **Footer Text**: Remove the "Thank you for creating with WordPress" text in the admin footer.
* **Update Nags**: Remove the all notices in the admin aready for WordPress®, plugins, and themes updates.
* **Admin Bar**: Remove the admin bar from the front-end of your site.
* **Howdy Text**: Remove the "Howdy" text next to the user's name in the admin bar.
* **Environment Type**: Show a label to the admin bar to indicate the environment type of your site (e.g. Development, Staging, Production).

### Media

In the Media section, you can find options to configure features that are related to the media library and the uploaded media files on your site.

* **Attachement Pages**: Disable the attachment page, or [enable it back on WordPress 6.4 or later](https://make.wordpress.org/core/2023/10/16/changes-to-attachment-pages/).
* **Attachment Slug**: Use random URL for Media pages to prevent slug conflicts with other post types.
* **Infinite Scroll**: Bring back the infinite scroll feature on the Media Library screen.
* **Image Quality**: Change the default image quality for uploaded images (Currently, only support for JPEG images).

### Site

This section provides some options to control your site access, some assets (scripts, styles, and images) loaded on the site, and some page meta tags or data.

* **Access**: Make your site public, allow only logged-in users to access your site, or put it in maintenance mode.

#### Assets

* **Emojis**: Improve the performance of your site by removing the emoji scripts and styles.
* **Scripts Version**: Remove the version query string from the scripts and styles URL.
* **jQuery Migrate**: Don't need jQuery Migrate? You can remove it from your site.

### Metadata

* **RSD Link**: Remove the Really Simple Discovery (RSD) tag.
* **Generator Meta Tag**: Remove the generator meta tag to not expose the WordPress® version of your site.
* **Shortlink**: Remove the shortlink meta tag from the site header.

### Security

This section provides some options to help you secure your site by disabling some features that may be used by attackers to exploit your site.

* **File Edit**: Disable the file editor in the admin area that allows you to edit the theme and plugin files.
* **XML-RPC**: Disable the XML-RPC endpoint.
* **API Authentication**: Force all request to REST API to be authenticated.

### Login

* **Identifier**: Change the login identifier to use just username, or email, or both.
* **Obfuscate Error**: Make it harder to guess which part of the login credentials is incorrect.
* **Block Bots**: Block known bots from accessing your site login page.

### Passwords

* **Application Passwords**: Prevent users from generating Application Passwords, and remove the related interface elements from the user profile.

### Advanced

In the Advanced section, you can find some features that are more advanced and may require some technical knowledge before you can decide to turn them off. Use with caution.

* **Updates**: Enable or disable updates and automatic updates for WordPress®, plugins, and themes.
* **Cron**: Disable the WP-Cron feature and use a real cron job instead.
* **Heartbeat**: Disable the Heartbeat API or configure it to use less resources.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/syntatis-feature-flipper` directory, or install the plugin through the WordPress 'Plugins' screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the 'Settings → Flipper' screen to configure the plugin.

[More info on installing plugins](https://wordpress.org/documentation/article/manage-plugins/#installing-plugins)

## Frequently Asked Questions

### Is it compatible with WordPress Multisite?

Currently, no. But it is in the plan to support WordPress Multisite in the future.

## Contributing
