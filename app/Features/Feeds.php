<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Action;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use const PHP_INT_MIN;

final class Feeds implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (Option::isOn('feeds')) {
			return;
		}

		$hook->parse($this);
		$hook->addFilter('feed_links_show_posts_feed', '__return_false', 99); // Remove RSS feed links.
		$hook->addFilter('feed_links_show_comments_feed', '__return_false', 99); // Remove all extra RSS feed links.
	}

	#[Action(name: 'do_feed', priority: PHP_INT_MIN)]
	#[Action(name: 'do_feed_rdf', priority: PHP_INT_MIN)]
	#[Action(name: 'do_feed_rss', priority: PHP_INT_MIN)]
	#[Action(name: 'do_feed_rss2', priority: PHP_INT_MIN)]
	#[Action(name: 'do_feed_atom', priority: PHP_INT_MIN)]
	#[Action(name: 'do_feed_rss2_comments', priority: PHP_INT_MIN)]
	#[Action(name: 'do_feed_atom_comments', priority: PHP_INT_MIN)]
	public function toHomepage(): void
	{
		wp_redirect(home_url());
		exit;
	}
}
