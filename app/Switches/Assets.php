<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Switches;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;
use WP_Scripts;

use function array_diff;

class Assets implements Hookable
{
	public function hook(Hook $hook): void
	{
		// 1. Emojis.
		if (! Option::get('emojis')) {
			/**
			 * WordPress 6.4 deprecated the use of `print_emoji_styles` function, but it has
			 * been retained for backward compatibility purposes.
			 *
			 * @see https://make.wordpress.org/core/2023/10/17/replacing-hard-coded-style-tags-with-wp_add_inline_style/
			 */
			$hook->removeAction('wp_print_styles', 'print_emoji_styles');
			$hook->removeAction('wp_head', 'print_emoji_detection_script', 7);
			$hook->removeAction('admin_print_scripts', 'print_emoji_detection_script');
			$hook->removeAction('admin_print_styles', 'print_emoji_styles');
			$hook->removeFilter('the_content_feed', 'wp_staticize_emoji');
			$hook->removeFilter('comment_text_rss', 'wp_staticize_emoji');
			$hook->removeFilter('wp_mail', 'wp_staticize_emoji_for_email');
		}

		// 2. Scripts Version.
		if (! Option::get('scripts_version')) {
			$callback = static function (string $src): string {
				return remove_query_arg('ver', $src);
			};
			$hook->addFilter('script_loader_src', $callback);
			$hook->addFilter('style_loader_src', $callback);
		}

		// 3. jQuery Migrate.
		if (Option::get('jquery_migrate')) {
			return;
		}

		$hook->addAction('wp_default_scripts', static function (WP_Scripts $scripts): void {
			if (empty($scripts->registered['jquery'])) {
				return;
			}

			$script = $scripts->registered['jquery'];
			if (! $script->deps) {
				return;
			}

			$script->deps = array_diff($script->deps, ['jquery-migrate']);
		});
	}
}
