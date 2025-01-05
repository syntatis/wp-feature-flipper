<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Comments;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\General;
use Syntatis\Tests\WPTestCase;
use WP_Admin_Bar;
use WP_Block_Type_Registry;

use const PHP_INT_MAX;

/**
 * @group feature-comments
 * @group module-general
 */
class CommentsTest extends WPTestCase
{
	private Hook $hook;
	private Comments $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public static function set_up_before_class(): void
	{
		parent::set_up_before_class();

		require_once ABSPATH . WPINC . '/class-wp-admin-bar.php';
	}

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
		unregister_post_type('product');

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

		$wpAdminBar = $this->getStandardAdminbar();
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

	private function getStandardAdminbar(): WP_Admin_Bar
	{
		$wpAdminBar = $GLOBALS['wp_admin_bar'] ?? new WP_Admin_Bar();

		_wp_admin_bar_init();
		do_action_ref_array('admin_bar_menu', [&$wpAdminBar]);

		return $wpAdminBar;
	}
}
