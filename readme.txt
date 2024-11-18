=== Feature Flipper ===

Contributors: tfirdaus
Tags: disable, rss, emojis, xmlrpc, gutenberg
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 1.3.0
Requires PHP: 7.4
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Easily switch some features in WordPress, on and off.

== Description ==

WordPress comes with a lot of features. Some of them are useful and essential, while there are probably some that you do not need. This plugin allows you to switch some of these features, on and off, easily. Several features that this plugin supports are:

- **Gutenberg**: Gutenberg has become the default editor in WordPress on both Page and Post type. But if you are not using it, you can disable it to switch back to the Classic Editor.
- **Emojis**: Emojis are fun. But WordPress loads some scripts to support rendering emojis. If you do not use Emojis in your content, you can disable them to remove the additional weight from the scripts.
- **XML-RPC**: XML-RPC is a remote procedure call protocol that allows you to communicate with your WordPress site. If you do not use any services that require XML-RPC, you can disable it which helps reducing the attack surface of your site.
- **jQuery Migrate**: jQuery Migrate is a JavaScript library that helps you to migrate your jQuery code to the latest version. If you are not using any old jQuery code, you can disable it to remove the additional weight of the library from your site.
- **Post Embeds**: Post Embeds allow you to embed posts from your site into another site, or within your own site. If you do not use this feature, you can disable it to prevent others from embedding your posts.
- **Attachment Slug**: WordPress, by default, will automaically create a slug for your attachment URLs from the media file name. This could cause issues when the slug is conflicting with your post or page slug. To avoid attachment reserving the slug, you can disable this default behaviour. This plugin will randomly generate a unique slug for your attachment URLs.
- And more...

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/syntatis-feature-flipper` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings -> Flipper screen to configure the plugin.

[More info on installing plugins](https://wordpress.org/documentation/article/manage-plugins/#installing-plugins)

== Frequently Asked Questions ==

= How do I disable a feature? =

To disable a feature, you can go to the Settings -> Flipper screen in your WordPress admin dashboard. You will see a list of features that you can enable or disable. Simply click the toggle button to switch the feature on or off.

= How do I suggest a new feature? =

You can suggest a new feature by creating a new issue in the [plugin's GitHub repository](https://github.com/syntatis/wp-feature-flipper).
