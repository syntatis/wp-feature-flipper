<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Comments;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\General;
use Syntatis\Tests\WPTestCase;

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
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new Comments();
		// $this->instance->hook($this->hook);
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
}
