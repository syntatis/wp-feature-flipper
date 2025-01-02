<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Attachment;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-attachment
 * @group module-media
 */
class AttachmentTest extends WPTestCase
{
	private Hook $hook;
	private Attachment $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new Attachment();
		$this->instance->hook($this->hook);
	}

	/** @testdox should return the default value */
	public function testOptionDefault(): void
	{
		$this->assertEquals('0', get_option('wp_attachment_pages_enabled'));
		$this->assertFalse(Option::get('attachment_page'));
	}

	/** @testdox should return the default as true when the option is set to 1 */
	public function testOptionDefaultEnabled(): void
	{
		update_option('wp_attachment_pages_enabled', '1');

		$this->assertEquals('1', get_option('wp_attachment_pages_enabled'));
		$this->assertTrue(Option::get('attachment_page'));
	}

	/** @testdox should update the option */
	public function testOptionUpdated(): void
	{
		Option::update('attachment_page', true);

		$this->assertEquals('1', get_option('wp_attachment_pages_enabled'));
		$this->assertTrue(Option::get('attachment_page'));

		Option::update('attachment_page', false);

		$this->assertEquals('0', get_option('wp_attachment_pages_enabled'));
		$this->assertFalse(Option::get('attachment_page'));
	}
}
