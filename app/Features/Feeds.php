<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;

use const PHP_INT_MIN;

final class Feeds implements Hookable
{
	public function hook(Hook $hook): void
	{
		// Disable feeds.
		$hook->addAction('do_feed', [self::class, 'redirect'], PHP_INT_MIN);
		$hook->addAction('do_feed_rdf', [self::class, 'redirect'], PHP_INT_MIN);
		$hook->addAction('do_feed_rss', [self::class, 'redirect'], PHP_INT_MIN);
		$hook->addAction('do_feed_rss2', [self::class, 'redirect'], PHP_INT_MIN);
		$hook->addAction('do_feed_atom', [self::class, 'redirect'], PHP_INT_MIN);

		// Disable comments feeds.
		$hook->addAction('do_feed_rss2_comments', [self::class, 'redirect'], PHP_INT_MIN);
		$hook->addAction('do_feed_atom_comments', [self::class, 'redirect'], PHP_INT_MIN);

		// Remove RSS feed links.
		$hook->addFilter('feed_links_show_posts_feed', '__return_false', 99);

		// Remove all extra RSS feed links.
		$hook->addFilter('feed_links_show_comments_feed', '__return_false', 99);
	}

	public static function redirect(): void
	{
		wp_redirect(home_url());
		exit;
	}
}
