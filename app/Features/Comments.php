<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Action;
use SSFV\Codex\Foundation\Hooks\Filter;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Admin;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Admin_Bar;
use WP_Block_Type_Registry;
use WP_Comment;
use WP_Comment_Query;

use function in_array;
use function is_array;
use function is_int;
use function is_numeric;
use function is_readable;
use function is_string;
use function remove_post_type_support;
use function strip_tags;

use const PHP_INT_MAX;

final class Comments implements Hookable
{
	private const EXCLUDE_POST_TYPES = ['product'];

	public function hook(Hook $hook): void
	{
		if (Option::isOn('comments')) {
			return;
		}

		$hook->parse($this);

		/**
		 * Handle feed links and page for comments.
		 */
		$hook->addFilter('feed_links_show_comments_feed', '__return_false', PHP_INT_MAX, 2);
		$hook->addFilter('post_comments_feed_link', '__return_empty_string', PHP_INT_MAX);
		$hook->addFilter('post_comments_feed_link_html', '__return_empty_string', PHP_INT_MAX);
	}

	#[Action(name: 'admin_init', priority: PHP_INT_MAX)]
	public function removePostTypeSupport(): void
	{
		foreach (get_post_types_by_support('comments') as $postType) {
			if (in_array($postType, self::EXCLUDE_POST_TYPES, true)) {
				continue;
			}

			remove_post_type_support($postType, 'comments');
			remove_post_type_support($postType, 'trackbacks');
		}
	}

	#[Action(name: 'do_meta_boxes', priority: PHP_INT_MAX)]
	public function removePostMetabox(string $postType): void
	{
		if (in_array($postType, self::EXCLUDE_POST_TYPES, true)) {
			return;
		}

		remove_meta_box('commentstatusdiv', $postType, 'normal');
		remove_meta_box('commentsdiv', $postType, 'normal');
	}

	#[Action(name: 'admin_menu', priority: PHP_INT_MAX)]
	public function removeAdminMenu(): void
	{
		if (Admin::isScreen('comment.php') || Admin::isScreen('edit-comments.php')) {
			wp_die(
				esc_html__('Comments are disabled.', 'syntatis-feature-flipper'),
				esc_html(strip_tags(get_admin_page_title())),
				['response' => 403],
			);
		}

		remove_menu_page('edit-comments.php');

		if (Admin::isScreen('options-discussion.php')) {
			wp_die(
				esc_html__('Comments are disabled.', 'syntatis-feature-flipper'),
				esc_html(strip_tags(get_admin_page_title())),
				['response' => 403],
			);
		}

		remove_submenu_page('options-general.php', 'options-discussion.php');
	}

	#[Action(name: 'admin_bar_menu', priority: PHP_INT_MAX)]
	public function removeAdminBarMenu(WP_Admin_Bar $wpAdminBar): void
	{
		$wpAdminBar->remove_node('comments');
	}

	#[Action(name: 'init', priority: PHP_INT_MAX)]
	public function unregisterBlocksServer(): void
	{
		$blocks = WP_Block_Type_Registry::get_instance()->get_all_registered();
		$commentBlocks = [
			'core/comment-author-name',
			'core/comment-content',
			'core/comment-date',
			'core/comment-edit-link',
			'core/comment-reply-link',
			'core/comment-template',
			'core/comments',
			'core/comments-pagination',
			'core/comments-pagination-next',
			'core/comments-pagination-numbers',
			'core/comments-pagination-previous',
			'core/comments-title',
			'core/latest-comments',
			'core/post-comments-form',
			'core/post-comments',
		];

		foreach ($commentBlocks as $block) {
			if (! isset($blocks[$block])) {
				continue;
			}

			unregister_block_type($block);
		}
	}

	#[Action(name: 'enqueue_block_editor_assets', priority: PHP_INT_MAX)]
	public function unregisterBlocksClient(): void
	{
		$assetFile = App::dir('dist/assets/comments/index.asset.php');

		/** @phpstan-var array{dependencies?:array<string>,version?:string} $asset */
		$asset = is_readable($assetFile) ? require $assetFile : [];
		$asset['dependencies'] ??= [];
		$asset['version'] ??= null;

		wp_enqueue_script(
			App::name() . '-comments',
			App::url('dist/assets/comments/index.js'),
			$asset['dependencies'],
			$asset['version'],
			true,
		);
	}

	/**
	 * @see https://github.com/WordPress/WordPress/blob/mster/wp-includes/class-wp-xmlrpc-server.php#L70 For the full list of XML-RPC methods.
	 *
	 * @param array<string,string> $methods
	 *
	 * @return array<string,string>
	 */
	#[Filter(name: 'xmlrpc_methods', priority: PHP_INT_MAX)]
	public function xmlrpcMethods(array $methods): array
	{
		unset($methods['wp.deleteComment']);
		unset($methods['wp.editComment']);
		unset($methods['wp.getComment']);
		unset($methods['wp.getCommentCount']);
		unset($methods['wp.getCommentStatusList']);
		unset($methods['wp.getComments']);
		unset($methods['wp.newComment']);

		return $methods;
	}

	/**
	 * @param array<string,mixed> $endpoints
	 *
	 * @return array<string,mixed>
	 */
	#[Filter(name: 'rest_endpoints', priority: PHP_INT_MAX)]
	public function restEndpoints(array $endpoints): array
	{
		unset($endpoints['/wp/v2/comments']);
		unset($endpoints['/wp/v2/comments/(?P<id>[\d]+)']);

		return $endpoints;
	}

	/**
	 * @param array<int>|array<WP_Comment>|null $comments
	 *
	 * @return array<int>|array<WP_Comment>|null
	 */
	#[Filter(name: 'comments_pre_query', priority: PHP_INT_MAX, acceptedArgs: 2)]
	public function commentsPreQuery(array|null $comments, WP_Comment_Query $query): array|null
	{
		// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress core variable.
		$postType = $query->query_vars['post_type'] ?? '';
		$postId = $query->query_vars['post_id'] ?? 0;
		// phpcs:enable

		$postId = is_numeric($postId) ? absint($postId) : null;

		if (! is_int($postId)) {
			return $comments;
		}

		$postIdType = (string) get_post_type($postId);
		$postType = is_string($postType) ? $postType : $postIdType;

		if (Admin::isScreen('dashboard')) {
			if ($postType === '' || ! in_array($postType, self::EXCLUDE_POST_TYPES, true)) {
				return [];
			}

			return $comments;
		}

		if (! is_admin()) {
			if ((bool) $postType && ! in_array($postType, self::EXCLUDE_POST_TYPES, true)) {
				return [];
			}

			return $comments;
		}

		return $comments;
	}

	/**
	 * Filter the number of comments to show on the "At a glance" widget in the
	 * Dashboard.
	 *
	 * @param object|array<string,int> $count The comment count object or array.
	 *
	 * @return object|array<string,int>
	 */
	#[Filter(name: 'wp_count_comments', priority: PHP_INT_MAX, acceptedArgs: 2)]
	public function countComments(object|array $count, int $postId): object|array
	{
		if (
			(Admin::isScreen('dashboard') && $postId === 0) ||
			! in_array(get_post_type($postId), self::EXCLUDE_POST_TYPES, true)
		) {
			return (object) [
				'approved' => 0,
				'awaiting_moderation' => 0,
				'spam' => 0,
				'trash' => 0,
				'post-trashed' => 0,
				'total_comments' => 0,
				'all' => 0,
				'moderated' => 0,
			];
		}

		return is_array($count) && $count !== [] ? (object) $count : $count;
	}

	/**
	 * Filter the output of comments to be rendered in the theme template.
	 *
	 * @param array<WP_Comment> $comments
	 *
	 * @return array<WP_Comment>
	 */
	#[Filter(name: 'comments_array', priority: PHP_INT_MAX, acceptedArgs: 2)]
	public function commentsArray(array $comments, int $postId): array
	{
		if (! in_array(get_post_type($postId), self::EXCLUDE_POST_TYPES, true)) {
			return [];
		}

		return $comments;
	}

	/**
	 * Filter whether the current post is open for comments.
	 */
	#[Filter(name: 'comments_open', priority: PHP_INT_MAX, acceptedArgs: 2)]
	public function commentsOpen(bool $isOpen, int $postId): bool
	{
		if (! in_array(get_post_type($postId), self::EXCLUDE_POST_TYPES, true)) {
			return false;
		}

		return $isOpen;
	}

	/**
	 * Filter the number of comments a post has to show on the theme template.
	 */
	#[Filter(name: 'get_comments_number', priority: PHP_INT_MAX, acceptedArgs: 2)]
	public function getCommentsNumber(int $number, int $postId): int
	{
		if (! in_array(get_post_type($postId), self::EXCLUDE_POST_TYPES, true)) {
			return 0;
		}

		return $number;
	}

	/**
	 * Filter whether the current post is open for pings.
	 */
	#[Filter(name: 'pings_open', priority: PHP_INT_MAX, acceptedArgs: 2)]
	public function pingsOpen(bool $isOpen, int $postId): bool
	{
		if (! in_array(get_post_type($postId), self::EXCLUDE_POST_TYPES, true)) {
			return false;
		}

		return $isOpen;
	}
}
