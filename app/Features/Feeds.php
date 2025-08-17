<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use const PHP_INT_MIN;

final class Feeds implements Hookable
{
	public function hook(Hook $hook): void
	{
		// Disable feeds.
		$hook->addAction('do_feed', [$this, 'toHomepage'], PHP_INT_MIN);
		$hook->addAction('do_feed_rdf', [$this, 'toHomepage'], PHP_INT_MIN);
		$hook->addAction('do_feed_rss', [$this, 'toHomepage'], PHP_INT_MIN);
		$hook->addAction('do_feed_rss2', [$this, 'toHomepage'], PHP_INT_MIN);
		$hook->addAction('do_feed_atom', [$this, 'toHomepage'], PHP_INT_MIN);

		// Disable comments feeds.
		$hook->addAction('do_feed_rss2_comments', [$this, 'toHomepage'], PHP_INT_MIN);
		$hook->addAction('do_feed_atom_comments', [$this, 'toHomepage'], PHP_INT_MIN);

		// Remove RSS feed links.
		$hook->addFilter('feed_links_show_posts_feed', '__return_false', 99);

		// Remove all extra RSS feed links.
		$hook->addFilter('feed_links_show_comments_feed', '__return_false', 99);
	}

	public function toHomepage(): void
	{
		wp_redirect(home_url());
		exit;
	}
}
