<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use _WP_Dependency;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Scripts;

use function array_diff;

final class Site implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! Option::isOn('emojis')) {
			/**
			 * WordPress 6.4 deprecated the use of `print_emoji_styles` function, but it has
			 * been retained for backward compatibility purposes.
			 *
			 * @see https://make.wordpress.org/core/2023/10/17/replacing-hard-coded-style-tags-with-wp_add_inline_style/
			 */
			$hook->removeAction('admin_print_scripts', 'print_emoji_detection_script');
			$hook->removeAction('admin_print_styles', 'print_emoji_styles');
			$hook->removeAction('wp_head', 'print_emoji_detection_script', 7);
			$hook->removeAction('wp_print_styles', 'print_emoji_styles');
			$hook->removeFilter('comment_text_rss', 'wp_staticize_emoji');
			$hook->removeFilter('the_content_feed', 'wp_staticize_emoji');
			$hook->removeFilter('wp_mail', 'wp_staticize_emoji_for_email');
		}

		if (! Option::isOn('scripts_version')) {
			$callback = static fn (string $src): string => remove_query_arg('ver', $src);

			$hook->addFilter('script_loader_src', $callback);
			$hook->addFilter('style_loader_src', $callback);
		}

		if (! Option::isOn('jquery_migrate')) {
			$hook->addAction('wp_default_scripts', static function (WP_Scripts $scripts): void {
				$jquery = $scripts->query('jquery', 'registered');

				if (! $jquery instanceof _WP_Dependency) {
					return;
				}

				$jquery->deps = array_diff($jquery->deps, ['jquery-migrate']);
			});
		}

		if (! Option::isOn('rsd_link')) {
			$hook->removeAction('wp_head', 'rsd_link');
		}

		if (! Option::isOn('generator_tag')) {
			$hook->removeAction('wp_head', 'wp_generator');
		}

		if (Option::isOn('shortlink')) {
			return;
		}

		$hook->removeAction('wp_head', 'wp_shortlink_wp_head');
		$hook->removeAction('wp_head', 'wp_shortlink_header');
	}
}
