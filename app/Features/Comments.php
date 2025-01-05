<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Admin_Bar;
use WP_Block_Type_Registry;
use WP_Comment;

use function is_readable;
use function remove_post_type_support;

use const PHP_INT_MAX;

class Comments implements Hookable
{
	use WithAdmin;

	public function hook(Hook $hook): void
	{
		if (Option::isOn('comments')) {
			return;
		}

		$hook->addAction('admin_bar_menu', [$this, 'removeAdminBarMenu'], PHP_INT_MAX);
		$hook->addAction('admin_init', [$this, 'removePostTypeSupport'], PHP_INT_MAX);
		$hook->addAction('admin_menu', [$this, 'removeAdminMenu'], PHP_INT_MAX);
		$hook->addAction('init', [$this, 'removeBlocks'], PHP_INT_MAX);
		$hook->addAction('enqueue_block_editor_assets', [$this, 'disableOnBlockEditor'], PHP_INT_MAX);
		$hook->addAction('do_meta_boxes', [$this, 'removePostMetabox'], PHP_INT_MAX);
		$hook->addFilter('rest_endpoints', [$this, 'restEndpoints'], PHP_INT_MAX);
		$hook->addFilter('xmlrpc_methods', [$this, 'xmlrpcMethods'], PHP_INT_MAX);
		$hook->addFilter('comments_array', static fn () => [], PHP_INT_MAX);
		$hook->addFilter('comments_open', static fn () => false, PHP_INT_MAX);
		$hook->addFilter('comments_pre_query', static fn () => 0, PHP_INT_MAX);
		$hook->addFilter('pings_open', static fn () => false, PHP_INT_MAX);
	}

	public function removePostTypeSupport(): void
	{
		foreach (get_post_types() as $postType) {
			if (! post_type_supports($postType, 'comments')) {
				continue;
			}

			remove_post_type_support($postType, 'comments');
			remove_post_type_support($postType, 'trackbacks');
		}
	}

	public function removeAdminMenu(): void
	{
		$pagenow = $GLOBALS['pagenow'] ?? '';

		if ($pagenow === 'comment.php' || $pagenow === 'edit-comments.php') {
			wp_die(esc_html__('Comments are disabled.', 'syntatis-feature-flipper'), '', ['response' => 403]);
		}

		remove_menu_page('edit-comments.php');

		if ($pagenow === 'options-discussion.php') {
			wp_die(esc_html__('Comments are disabled.', 'syntatis-feature-flipper'), '', ['response' => 403]);
		}

		remove_submenu_page('options-general.php', 'options-discussion.php');
	}

	public function removeAdminBarMenu(WP_Admin_Bar $wpAdminBar): void
	{
		$wpAdminBar->remove_node('comments');
	}

	public function removePostMetabox(string $postType): void
	{
		remove_meta_box('commentstatusdiv', $postType, 'normal');
		remove_meta_box('commentsdiv', $postType, 'normal');
	}

	/**
	 * @param array<int>|array<WP_Comment>|null $comments
	 *
	 * @return array<int>|array<WP_Comment>|null
	 */
	public function commentsPreQuery(?array $comments): ?array
	{
		return null;
	}

	/**
	 * @param array<string,string> $methods
	 *
	 * @return array<string,string>
	 */
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
	public function restEndpoints(array $endpoints): array
	{
		unset($endpoints['/wp/v2/comments']);
		unset($endpoints['/wp/v2/comments/(?P<id>[\d]+)']);

		return $endpoints;
	}

	public function removeBlocks(): void
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

	public function disableOnBlockEditor(): void
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
}
