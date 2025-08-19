<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SFFV\Codex\Contracts\Extendable;
use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use SFFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\CommentLength;
use Syntatis\FeatureFlipper\Features\Comments;
use Syntatis\FeatureFlipper\Features\Embeds;
use Syntatis\FeatureFlipper\Features\Feeds;
use Syntatis\FeatureFlipper\Features\Gutenberg;
use Syntatis\FeatureFlipper\Helpers\Option;

use function array_filter;
use function define;
use function defined;
use function is_array;
use function is_numeric;
use function is_string;
use function str_starts_with;

use const PHP_INT_MAX;

final class General implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter(
			Option::hook('default:block_based_widgets'),
			static fn () => get_theme_support('widgets-block-editor'),
			PHP_INT_MAX,
		);

		$hook->addFilter(
			'use_widgets_block_editor',
			static fn (bool $value) => Option::isOn('block_based_widgets') ?? $value,
			PHP_INT_MAX,
		);

		$hook->addFilter('pre_ping', static function (&$links): void {
			if (Option::isOn('self_ping') || ! is_array($links)) {
				return;
			}

			$links = array_filter(
				$links,
				static fn ($link) => is_string($link) && ! str_starts_with($link, home_url()),
			);
		}, 99);

		/**
		 * When revisions are disabled, force the maximum revisions to 0 to prevent
		 * WordPress from creating any revisions.
		 */
		if (! Option::isOn('revisions') && ! defined('WP_POST_REVISIONS')) {
			define('WP_POST_REVISIONS', 0);
		}

		$hook->addFilter(
			'wp_revisions_to_keep',
			static function ($num) {
				if (! Option::isOn('revisions')) {
					return 0;
				}

				if (! Option::isOn('revisions_max_enabled')) {
					return $num;
				}

				$max = Option::get('revisions_max');

				return is_numeric($max) ? (int) $max : $num;
			},
			PHP_INT_MAX,
		);
	}

	/** @inheritDoc */
	public function getInstances(ContainerInterface $container): iterable
	{
		$minLengthEnabled = Option::isOn('comment_min_length_enabled');
		$maxLengthEnabled = Option::isOn('comment_max_length_enabled');

		yield 'comment_length' => $minLengthEnabled || $maxLengthEnabled ?
			new CommentLength($minLengthEnabled, $maxLengthEnabled) :
			null;

		yield 'comments' => ! Option::isOn('comments') ? new Comments() : null;
		yield 'embeds' => ! Option::isOn('embed') ? new Embeds() : null;
		yield 'feeds' => ! Option::isOn('feeds') && ! is_admin() ? new Feeds() : null;
		yield 'gutenberg' => is_admin() ? new Gutenberg() : null;
	}
}
