<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Attachment;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use function in_array;
use function version_compare;

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

	/** @testdox should return the default value (wp >= 6.4) */
	public function testOptionDefault(): void
	{
		if (! version_compare($GLOBALS['wp_version'], '6.4', '>=')) {
			$this->markTestSkipped('This test is only for WordPress 6.4 or newer.');
		}

		$this->assertEquals('0', get_option('wp_attachment_pages_enabled'));
		$this->assertFalse(Option::isOn('attachment_page'));
	}

	/** @testdox should return the default value (wp < 6.4) */
	public function testOptionDefaultVerLte64(): void
	{
		if (! version_compare($GLOBALS['wp_version'], '6.4', '<')) {
			$this->markTestSkipped('This test is only for WordPress 6.3 or older.');
		}

		$this->assertFalse(get_option('wp_attachment_pages_enabled'));
		$this->assertTrue(Option::isOn('attachment_page'));
	}

	/** @testdox should return the default as true when the option is set to 1 */
	public function testOptionDefaultEnabled(): void
	{
		update_option('wp_attachment_pages_enabled', '1');

		$this->assertEquals('1', get_option('wp_attachment_pages_enabled'));
		$this->assertTrue(Option::isOn('attachment_page'));
	}

	/** @testdox should update the option and `wp_attachment_pages_enabled` */
	public function testOptionUpdated(): void
	{
		$wpVersion = $GLOBALS['wp_version'];

		if (version_compare($wpVersion, '6.4', '>=')) {
			$this->assertTrue(Option::update('attachment_page', true));
			$this->assertTrue(Option::isOn('attachment_page'));
			$this->assertSame('1', get_option('wp_attachment_pages_enabled'));
		} else {
			// Updating the option would fail since `true` is the default value in WordPress 6.3 or older.
			$this->assertFalse(Option::update('attachment_page', true));
			$this->assertTrue(Option::isOn('attachment_page'));

			/**
			 * On an older version, the `wp_attachment_pages_enabled` may not exists so it should return
			 * the default, or `1` if option is set after the upgrade.
			 *
			 * @see https://github.com/WordPress/WordPress/blob/712a4ea227fee52af3106072b5b3e51e82c2449a/wp-admin/includes/upgrade.php#L2373-L2374
			 */
			$value = in_array(get_option('wp_attachment_pages_enabled', null), ['1', null]) ? '1' : '0';

			$this->assertSame('1', $value);
		}

		$this->assertTrue(Option::update('attachment_page', false));
		$this->assertFalse(Option::isOn('attachment_page'));
		$this->assertEquals('0', get_option('wp_attachment_pages_enabled'));
	}
}
