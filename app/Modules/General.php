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
use Syntatis\FeatureFlipper\Helpers\Str;

use function array_filter;
use function define;
use function defined;
use function is_array;
use function is_int;
use function is_numeric;
use function is_string;
use function str_starts_with;
use function strip_tags;

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
				if (! is_array($links)) {
					return;
				}

				$links = array_filter(
					$links,
					static fn ($link) => is_string($link) && ! str_starts_with($link, home_url()),
				);
			}, 99);
		}

		if (Option::isOn('comment_min_length_enabled')) {
			$hook->addFilter('preprocess_comment', [$this, 'filterPreprocessComment'], PHP_INT_MAX);
		}

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

	/**
	 * Filter the comment content before it is processed.
	 *
	 * @param array{comment_content?:string|null} $commentData The comment data. {@see https://developer.wordpress.org/reference/hooks/preprocess_comment/}.
	 *
	 * @return array{comment_content?:string|null} The filtered comment data.
	 */
	public function filterPreprocessComment(array $commentData = []): array
	{
		if (! isset($commentData['comment_content'])) {
			return $commentData;
		}

		$minLength = Option::get('comment_min_length');
		$minLength = is_numeric($minLength) ? absint($minLength) : null;

		if ($minLength === null) {
			return $commentData;
		}

		$length = Str::length(
			strip_tags($commentData['comment_content']),
			get_bloginfo('charset'),
		);

		if (is_int($length) && $length < $minLength) {
			wp_die(
				esc_html(__('Comment\'s too short. Please add something more helpful.', 'syntatis-feature-flipper')),
				esc_html(__('Comment Error', 'syntatis-feature-flipper')),
				[
					'response' => 400,
					'back_link' => true,
				],
			);
		}

		return $commentData;
	}

	/**
	 * Proivide other features to load managed within the General section.
	 *
	 * @return iterable<object>
	 */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new Comments();
		yield new Embeds();
		yield new Feeds();
		yield new Gutenberg();
	}
}
