<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Concerns\WithPostTypes;
use Syntatis\FeatureFlipper\Features\Comments;
use Syntatis\FeatureFlipper\Features\Embeds;
use Syntatis\FeatureFlipper\Features\Feeds;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Post;

use function array_filter;
use function array_keys;
use function define;
use function defined;
use function in_array;
use function is_int;
use function is_numeric;
use function str_starts_with;

use const PHP_INT_MAX;

class General implements Hookable, Extendable
{
	use WithHookName;
	use WithPostTypes;

	public function hook(Hook $hook): void
	{
		$hook->addFilter('use_block_editor_for_post', [$this, 'filterUseBlockEditorForPost'], PHP_INT_MAX, 2);
		$hook->addFilter('use_widgets_block_editor', [$this, 'filterUseWidgetsBlockEditor'], PHP_INT_MAX);
		$hook->addFilter(
			self::defaultOptionName('gutenberg_post_types'),
			static fn () => array_keys(self::getPostTypes()),
		);
		$hook->addFilter(
			self::defaultOptionName('block_based_widgets'),
			static fn () => get_theme_support('widgets-block-editor'),
		);

		if (! (bool) Option::get('self_ping')) {
			$hook->addFilter('pre_ping', static function (&$links): void {
				$links = array_filter($links, static fn ($link) => ! str_starts_with($link, home_url()));
			}, 99);
		}

		$maxRevisions = Option::get('revisions_max');

		if (! (bool) Option::get('revisions')) {
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

	/**
	 * Filter the value to determine whether to use the block editor for a post.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/use_block_editor_for_post/
	 *
	 * @param int|WP_Post $post
	 */
	public function filterUseBlockEditorForPost(bool $value, $post): bool
	{
		// If the Gutenberg feature is disabled, force the classic editor.
		if (! (bool) Option::get('gutenberg')) {
			return false;
		}

		if (is_int($post)) {
			$post = get_post($post);
		}

		if ($post === null) {
			return $value;
		}

		return in_array(
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress convention.
			$post->post_type,
			(array) Option::get('gutenberg_post_types'),
			true,
		);
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		$features = $this->getFeatures();

		foreach ($features as $feature) {
			yield $feature;

			if (! ($feature instanceof Extendable)) {
				continue;
			}

			yield from $feature->getInstances($container);
		}
	}

	/** @return iterable<object> */
	private function getFeatures(): iterable
	{
		yield new Comments();
		yield new Embeds();
		yield new Feeds();
	}
}
