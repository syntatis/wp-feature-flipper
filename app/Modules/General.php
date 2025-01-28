<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\Comments;
use Syntatis\FeatureFlipper\Features\Embeds;
use Syntatis\FeatureFlipper\Features\Feeds;
use Syntatis\FeatureFlipper\Features\Gutenberg;
use Syntatis\FeatureFlipper\Helpers\Option;

use function array_filter;
use function define;
use function defined;
use function is_numeric;
use function str_starts_with;

use const PHP_INT_MAX;

final class General implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter('use_widgets_block_editor', [$this, 'filterUseWidgetsBlockEditor'], PHP_INT_MAX);
		$hook->addFilter(
			Option::hook('default:block_based_widgets'),
			static fn () => get_theme_support('widgets-block-editor'),
			PHP_INT_MAX,
		);

		if (! Option::isOn('self_ping')) {
			$hook->addFilter('pre_ping', static function (&$links): void {
				$links = array_filter($links, static fn ($link) => ! str_starts_with($link, home_url()));
			}, 99);
		}

		$maxRevisions = Option::get('revisions_max');

		if (! Option::isOn('revisions')) {
			if (! defined('WP_POST_REVISIONS')) {
				define('WP_POST_REVISIONS', 0);
			}

			$maxRevisions = 0;
		}

		$hook->addFilter(
			'wp_revisions_to_keep',
			static fn ($num) => is_numeric($maxRevisions) ?
				(int) $maxRevisions :
				$num,
			PHP_INT_MAX,
		);
	}

	/**
	 * Filter the value to determine whether to use the block editor for widgets.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/use_widgets_block_editor/
	 */
	public function filterUseWidgetsBlockEditor(bool $value): bool
	{
		$option = Option::get('block_based_widgets');

		if ($option === null) {
			return $value;
		}

		return (bool) $option === true;
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new Comments();
		yield new Embeds();
		yield new Feeds();
		yield new Gutenberg();
	}
}
