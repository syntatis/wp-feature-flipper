<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\Comments;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Comments\Comments;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\General;
use Syntatis\Tests\WithAdminBar;
use Syntatis\Tests\WPTestCase;
use WP_Block_Type_Registry;
use WP_Comment_Query;

use const PHP_INT_MAX;

/**
 * @group feature-comments
 * @group module-general
 */
class CommentsTest extends WPTestCase
{
	use WithAdminBar;

	private Hook $hook;
	private Comments $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new Comments();
		$this->instance->hook($this->hook);

		register_post_type(
			'product',
			[
				'public' => true,
				'supports' => ['comments', 'trackbacks'],
			],
		);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		unset($GLOBALS['pagenow']);
		unregister_post_type('product');
		self::tearDownAdminBar();

		parent::tear_down();
	}

	/** @testdox should have callback attached to hooks */
	public function testHook(): void
	{
		$this->assertFalse($this->hook->hasFilter('rest_endpoints', [$this->instance, 'filterRestEndpoints']));
		$this->assertFalse($this->hook->hasFilter('xmlrpc_methods', [$this->instance, 'filterXmlrpcMethods']));

		$this->assertFalse($this->hook->hasAction('admin_bar_menu', [$this->instance, 'removeAdminBarMenu']));
		$this->assertFalse($this->hook->hasAction('admin_init', [$this->instance, 'removePostTypeSupport']));
		$this->assertFalse($this->hook->hasAction('admin_menu', [$this->instance, 'removeAdminMenu']));
		$this->assertFalse($this->hook->hasAction('do_meta_boxes', [$this->instance, 'removePostMetabox']));

		$this->assertFalse($this->hook->hasAction('init', [$this->instance, 'unregisterBlocksServer']));
		$this->assertFalse($this->hook->hasAction('enqueue_block_editor_assets', [$this->instance, 'unregisterBlocksClient']));

		$this->assertFalse($this->hook->hasFilter('comments_pre_query', [$this->instance, 'filterCommentsPreQuery']));
		$this->assertFalse($this->hook->hasFilter('wp_count_comments', [$this->instance, 'filterCommentsCount']));

		$this->assertFalse($this->hook->hasFilter('comments_array', [$this->instance, 'filterCommentsArray']));
		$this->assertFalse($this->hook->hasFilter('comments_open', [$this->instance, 'filterCommentsOpen']));
		$this->assertFalse($this->hook->hasFilter('get_comments_number', [$this->instance, 'filterGetCommentsNumber']));
		$this->assertFalse($this->hook->hasFilter('pings_open', [$this->instance, 'filterPingsOpen']));

		$this->assertFalse($this->hook->hasFilter('feed_links_show_comments_feed', '__return_false'));
		$this->assertFalse($this->hook->hasFilter('post_comments_feed_link', '__return_empty_string'));
		$this->assertFalse($this->hook->hasFilter('post_comments_feed_link_html', '__return_empty_string'));

		// Disable the "comments" feature.
		Option::update('comments', false);

		// Reload the hooks.
		$this->instance->hook($this->hook);

		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('rest_endpoints', [$this->instance, 'filterRestEndpoints']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('xmlrpc_methods', [$this->instance, 'filterXmlrpcMethods']));

		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_bar_menu', [$this->instance, 'removeAdminBarMenu']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_init', [$this->instance, 'removePostTypeSupport']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('admin_menu', [$this->instance, 'removeAdminMenu']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('do_meta_boxes', [$this->instance, 'removePostMetabox']));

		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('init', [$this->instance, 'unregisterBlocksServer']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('enqueue_block_editor_assets', [$this->instance, 'unregisterBlocksClient']));

		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('comments_pre_query', [$this->instance, 'filterCommentsPreQuery']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('wp_count_comments', [$this->instance, 'filterCommentsCount']));

		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('comments_array', [$this->instance, 'filterCommentsArray']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('comments_open', [$this->instance, 'filterCommentsOpen']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('get_comments_number', [$this->instance, 'filterGetCommentsNumber']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('pings_open', [$this->instance, 'filterPingsOpen']));

		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('feed_links_show_comments_feed', '__return_false'));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('post_comments_feed_link', '__return_empty_string'));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('post_comments_feed_link_html', '__return_empty_string'));
	}

	/** @testdox should remove "comments" support from selected post types */
	public function testRemovePostTypeSupport(): void
	{
		$this->assertTrue(post_type_supports('page', 'comments'));
		$this->assertTrue(post_type_supports('post', 'comments'));
		$this->assertTrue(post_type_supports('post', 'trackbacks'));
		$this->assertTrue(post_type_supports('product', 'comments'));
		$this->assertTrue(post_type_supports('product', 'trackbacks'));

		$this->instance->removePostTypeSupport();

		$this->assertFalse(post_type_supports('post', 'comments'));
		$this->assertFalse(post_type_supports('page', 'comments'));
		$this->assertFalse(post_type_supports('page', 'trackbacks'));

		// WooCommerce product post type should not be affected.
		$this->assertTrue(post_type_supports('product', 'comments'));
		$this->assertTrue(post_type_supports('product', 'trackbacks'));
	}

	/**
	 * @dataProvider dataRemovePostMetabox
	 * @testdox should remove "comments" metabox from the selected post types
	 */
	public function testRemovePostMetabox(string $postType): void
	{
		add_meta_box('commentstatusdiv', 'Discussion', static fn () => '', $postType, 'normal');
		add_meta_box('commentsdiv', 'Comments', static fn () => '', $postType, 'normal');

		$this->instance->removePostMetabox($postType);

		$wpMetaboxes = (array) ($GLOBALS['wp_meta_boxes'] ?? []);

		$this->assertFalse($wpMetaboxes[$postType]['normal']['default']['commentstatusdiv']);
		$this->assertFalse($wpMetaboxes[$postType]['normal']['default']['commentsdiv']);
	}

	public static function dataRemovePostMetabox(): iterable
	{
		yield 'post' => ['post'];
		yield 'page' => ['page'];
	}

	/**
	 * @dataProvider dataRemovePostMetaboxExcluded
	 * @testdox should not remove "comments" metabox from the selected post types
	 */
	public function testRemovePostMetaboxExcluded(string $postType): void
	{
		add_meta_box('commentstatusdiv', 'Discussion', static fn () => '', $postType, 'normal');
		add_meta_box('commentsdiv', 'Comments', static fn () => '', $postType, 'normal');

		$this->instance->removePostMetabox($postType);

		$wpMetaboxes = (array) ($GLOBALS['wp_meta_boxes'] ?? []);

		$this->assertArrayHasKey('commentstatusdiv', $wpMetaboxes[$postType]['normal']['default']);
		$this->assertArrayHasKey('commentsdiv', $wpMetaboxes[$postType]['normal']['default']);
	}

	public static function dataRemovePostMetaboxExcluded(): iterable
	{
		yield 'product' => ['product'];
	}

	/** @testdox should remove "comments" menu in the admin bar */
	public function testRemoveAdminBarMenu(): void
	{
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		self::setUpAdminBar();

		$wpAdminBar = $GLOBALS['wp_admin_bar'];
		$node = $wpAdminBar->get_node('comments');

		$this->assertObjectHasProperty('id', $node);
		$this->assertSame('comments', $node->id);

		$this->instance->removeAdminBarMenu($wpAdminBar);

		$this->assertNull($wpAdminBar->get_node('comments'));
	}

	/** @testdox should remove comments-related block from core */
	public function testUnregisterBlocksServer(): void
	{
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
		$instance = WP_Block_Type_Registry::get_instance();

		foreach ($commentBlocks as $blockName) {
			$this->assertTrue($instance->is_registered($blockName));
		}

		$this->instance->unregisterBlocksServer();

		foreach ($commentBlocks as $blockName) {
			$this->assertFalse($instance->is_registered($blockName));
		}
	}

	/** @testdox should filter out the comments-related xmlrpc methods */
	public function testFilterXmlrpcMethods(): void
	{
		$methods = $this->instance->filterXmlrpcMethods([
			'wp.deleteComment' => 'wp_deleteComment',
			'wp.editComment' => 'wp_deleteComment',
			'wp.getComment' => 'wp_getComment',
			'wp.getCommentCount' => 'wp_deleteComment',
			'wp.getCommentStatusList' => 'wp_deleteComment',
			'wp.getComments' => 'wp_deleteComment',
			'wp.newComment' => 'wp_deleteComment',
			'wp.newPost' => 'wp_newPost',
		]);

		$this->assertArrayNotHasKey('wp.deleteComment', $methods);
		$this->assertArrayNotHasKey('wp.editComment', $methods);
		$this->assertArrayNotHasKey('wp.getComment', $methods);
		$this->assertArrayNotHasKey('wp.getCommentCount', $methods);
		$this->assertArrayNotHasKey('wp.getCommentStatusList', $methods);
		$this->assertArrayNotHasKey('wp.getComments', $methods);
		$this->assertArrayNotHasKey('wp.newComment', $methods);
		$this->assertArrayHasKey('wp.newPost', $methods);
	}

	/** @testdox should filter out comments-related rest endpoints */
	public function testFilterRestEndpoints(): void
	{
		$endpoints = $this->instance->filterRestEndpoints([
			'/wp/v2/comments' => [],
			'/wp/v2/comments/(?P<id>[\d]+)' => [],
			'/wp/v2/posts' => [],
		]);

		$this->assertArrayNotHasKey('/wp/v2/comments', $endpoints);
		$this->assertArrayNotHasKey('/wp/v2/comments/(?P<id>[\d]+)', $endpoints);
		$this->assertArrayHasKey('/wp/v2/posts', $endpoints);
	}

	/** @testdox should return empty array if the comments feature is disabled */
	public function testFilterCommentsQueryOnDashboard(): void
	{
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		set_current_screen('dashboard');

		// phpcs:ignore
		$GLOBALS['pagenow'] = 'index.php';

		$this->assertTrue(is_admin());
		$this->assertSame([], $this->instance->filterCommentsPreQuery([1], new WP_Comment_Query(['post_type' => ''])));
		$this->assertSame([], $this->instance->filterCommentsPreQuery([1], new WP_Comment_Query(['post_type' => 'post'])));

		// Post type is excluded.
		$this->assertSame([1], $this->instance->filterCommentsPreQuery([1], new WP_Comment_Query(['post_type' => 'product'])));
	}

	/** @testdox should return inherited value if the comments feature is disabled */
	public function testFilterCommentsQueryNotOnDashboard(): void
	{
		$postId = self::factory()->post->create(['post_type' => 'post']);
		$productId = self::factory()->post->create(['post_type' => 'product']);

		$this->assertFalse(is_admin());
		$this->assertSame(
			[],
			$this->instance->filterCommentsPreQuery(
				[1],
				new WP_Comment_Query([
					'post_type' => 'post',
					'post_id' => $postId,
				]),
			),
		);
		$this->assertSame(
			[],
			$this->instance->filterCommentsPreQuery(
				[1],
				new WP_Comment_Query([
					'post_type' => 'post',
					'post_id' => $postId,
				]),
			),
		);

		// Post type is excluded.
		$this->assertSame(
			[1],
			$this->instance->filterCommentsPreQuery(
				[1],
				new WP_Comment_Query([
					'post_type' => 'product',
					'post_id' => $productId,
				]),
			),
		);

		// Without ID.
		$this->assertSame(
			[],
			$this->instance->filterCommentsPreQuery(
				[1],
				new WP_Comment_Query(['post_type' => 'post']),
			),
		);

		$this->assertSame(
			[1],
			$this->instance->filterCommentsPreQuery(
				[1],
				new WP_Comment_Query(['post_type' => 'product']),
			),
		);
	}

	/** @testdox should filter out the comments count when queried on the dashboard */
	public function testFilterCommentsCountOnDashboard(): void
	{
		wp_set_current_user(self::factory()->user->create(['role' => 'administrator']));
		set_current_screen('dashboard');
		$GLOBALS['pagenow'] = 'index.php'; // phpcs:ignore

		$postId = self::factory()->post->create(['post_type' => 'post']);
		$productId = self::factory()->post->create(['post_type' => 'product']);

		$this->assertTrue(is_admin());
		$this->assertEquals(
			(object) [
				'approved' => 0,
				'awaiting_moderation' => 0,
				'spam' => 0,
				'trash' => 0,
				'post-trashed' => 0,
				'total_comments' => 0,
				'all' => 0,
				'moderated' => 0,
			],
			$this->instance->filterCommentsCount(
				[
					'approved' => 2,
					'awaiting_moderation' => 0,
					'spam' => 0,
					'trash' => 0,
					'post-trashed' => 0,
					'total_comments' => 0,
					'all' => 2,
					'moderated' => 0,
				],
				$postId,
			),
		);

		// Post type is excluded.
		$this->assertEquals(
			(object) [
				'approved' => 3,
				'awaiting_moderation' => 0,
				'spam' => 0,
				'trash' => 0,
				'post-trashed' => 0,
				'total_comments' => 0,
				'all' => 3,
				'moderated' => 0,
			],
			$this->instance->filterCommentsCount(
				[
					'approved' => 3,
					'awaiting_moderation' => 0,
					'spam' => 0,
					'trash' => 0,
					'post-trashed' => 0,
					'total_comments' => 0,
					'all' => 3,
					'moderated' => 0,
				],
				$productId,
			),
		);
	}

	/** @testdox should filter out the comments count when queried outside the dashboard */
	public function testFilterCommentsCountOutsideDashboard(): void
	{
		$postId = self::factory()->post->create(['post_type' => 'post']);
		$productId = self::factory()->post->create(['post_type' => 'product']);

		$this->assertFalse(is_admin());
		$this->assertEquals(
			(object) [
				'approved' => 0,
				'awaiting_moderation' => 0,
				'spam' => 0,
				'trash' => 0,
				'post-trashed' => 0,
				'total_comments' => 0,
				'all' => 0,
				'moderated' => 0,
			],
			$this->instance->filterCommentsCount(
				[
					'approved' => 2,
					'awaiting_moderation' => 0,
					'spam' => 0,
					'trash' => 0,
					'post-trashed' => 0,
					'total_comments' => 0,
					'all' => 2,
					'moderated' => 0,
				],
				$postId,
			),
		);

		// Post type is excluded.
		$this->assertEquals(
			(object) [
				'approved' => 3,
				'awaiting_moderation' => 0,
				'spam' => 0,
				'trash' => 0,
				'post-trashed' => 0,
				'total_comments' => 0,
				'all' => 3,
				'moderated' => 0,
			],
			$this->instance->filterCommentsCount(
				[
					'approved' => 3,
					'awaiting_moderation' => 0,
					'spam' => 0,
					'trash' => 0,
					'post-trashed' => 0,
					'total_comments' => 0,
					'all' => 3,
					'moderated' => 0,
				],
				$productId,
			),
		);
	}

	/**
	 * @dataProvider dataFilterCommentsArray
	 * @testdox should empty comments array
	 */
	public function testFilterCommentsArray(string $postType, array $expect): void
	{
		$postId = self::factory()->post->create(['post_type' => $postType]);

		$this->assertSame($expect, $this->instance->filterCommentsArray([1], $postId));
	}

	public static function dataFilterCommentsArray(): iterable
	{
		yield 'post' => ['post', []];
		yield 'product' => ['product', [1]];
	}

	/**
	 * @dataProvider dataFilterCommentsOpen
	 * @testdox should open or close comments
	 */
	public function testFilterCommentsOpen(string $postType, bool $expect): void
	{
		$postId = self::factory()->post->create(['post_type' => $postType]);

		$this->assertSame($expect, $this->instance->filterCommentsOpen(true, $postId));
	}

	public static function dataFilterCommentsOpen(): iterable
	{
		yield 'post' => ['post', false];
		yield 'product' => ['product', true];
	}

	/**
	 * @dataProvider dataFilterPingsOpen
	 * @testdox should open or close pings
	 */
	public function testFilterPingsOpen(string $postType, bool $expect): void
	{
		$postId = self::factory()->post->create(['post_type' => $postType]);

		$this->assertSame($expect, $this->instance->filterPingsOpen(true, $postId));
	}

	public static function dataFilterPingsOpen(): iterable
	{
		yield 'post' => ['post', false];
		yield 'product' => ['product', true];
	}

	/**
	 * @dataProvider dataFilterGetCommentsNumber
	 * @testdox should return the number of comments or 0
	 */
	public function testFilterGetCommentsNumber(string $postType, int $expect): void
	{
		$postId = self::factory()->post->create(['post_type' => $postType]);

		$this->assertSame($expect, $this->instance->filterGetCommentsNumber(2, $postId));
	}

	public static function dataFilterGetCommentsNumber(): iterable
	{
		yield 'post' => ['post', 0];
		yield 'product' => ['product', 2];
	}
}
