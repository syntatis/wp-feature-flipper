<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Switches;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\Config;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Embeds;
use Syntatis\FeatureFlipper\Option;

use function array_filter;
use function define;
use function defined;
use function str_starts_with;

class General implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter('syntatis/feature_flipper/settings', [$this, 'setSettings']);

		// 1. Gutenberg.
		if (! (bool) Option::get('gutenberg')) {
			$hook->addFilter('use_block_editor_for_post', '__return_false');
			$hook->addFilter('use_widgets_block_editor', '__return_false');
			$hook->addAction('wp_enqueue_scripts', static function (): void {
				wp_dequeue_style('classic-theme-styles');
				wp_dequeue_style('core-block-supports');
				wp_dequeue_style('global-styles');
				wp_dequeue_style('wp-block-library');
				wp_dequeue_style('wp-block-library-theme');
			}, 99);
		}

		$blockBasedWidgets = Option::get('block_based_widgets');

		if ($blockBasedWidgets !== null && ! (bool) $blockBasedWidgets) {
			$hook->addFilter('use_widgets_block_editor', '__return_false');
		}

		// 2. Heartbeat.
		if (! (bool) Option::get('heartbeat')) {
			$hook->addAction('init', static fn () => wp_deregister_script('heartbeat'), -99);
		}

		// 3. Self-ping.
		if (! (bool) Option::get('self_ping')) {
			$hook->addFilter('pre_ping', static function (&$links): void {
				$links = array_filter($links, static fn ($link) => ! str_starts_with($link, home_url()));
			}, 99);
		}

		// 4. Cron.
		if (! (bool) Option::get('cron') && ! defined('DISABLE_WP_CRON')) {
			define('DISABLE_WP_CRON', true);
		}

		// 5. Embed.
		if (! (bool) Option::get('embed')) {
			(new Embeds())->hook($hook);
		}

		// 6. Feeds.
		if ((bool) Option::get('feeds')) {
			return;
		}

		// Disable feeds.
		$hook->addAction('do_feed', [$this, 'toHomepage'], -99);
		$hook->addAction('do_feed_rdf', [$this, 'toHomepage'], -99);
		$hook->addAction('do_feed_rss', [$this, 'toHomepage'], -99);
		$hook->addAction('do_feed_rss2', [$this, 'toHomepage'], -99);
		$hook->addAction('do_feed_atom', [$this, 'toHomepage'], -99);

		// Disable comments feeds.
		$hook->addAction('do_feed_rss2_comments', [$this, 'toHomepage'], -99);
		$hook->addAction('do_feed_atom_comments', [$this, 'toHomepage'], -99);

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

	/**
	 * @param array<mixed> $data
	 *
	 * @return array<mixed>
	 */
	public function setSettings(array $data): array
	{
		$optionName = Config::get('app.option_prefix') . 'block_based_widgets';
		$value = Option::get('block_based_widgets');

		if ($value === null) {
			$data[$optionName] = get_theme_support('widgets-block-editor');
		}

		return $data;
	}
}
