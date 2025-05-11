<?php

declare(strict_types=1);

namespace Syntatis\Tests\Modules;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\General;
use Syntatis\FeatureFlipper\Modules\Mail;
use Syntatis\Tests\WPTestCase;

/** @group module-general */
class MailTest extends WPTestCase
{
	private Hook $hook;
	private Mail $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new Mail();
		$this->instance->hook($this->hook);
	}

	public function testMailFromAddressDefault(): void
	{
		$this->assertNull(Option::get('mail_from_address'));
	}

	public function testMailFromAddressFilter(): void
	{
		$this->assertSame('wordpress@example.org', apply_filters('wp_mail_from', 'wordpress@example.org'));

		Option::update('mail_from_address', 'no-reply@example.org');

		$this->assertSame('no-reply@example.org', Option::get('mail_from_address'));
		$this->assertSame('no-reply@example.org', apply_filters('wp_mail_from', 'wordpress@example.org'));
	}

	public function testMailFromNameDefault(): void
	{
		$this->assertNull(Option::get('mail_from_name'));
	}

	public function testMailFromAddressName(): void
	{
		$this->assertSame('WordPress', apply_filters('wp_mail_from_name', 'WordPress'));

		Option::update('mail_from_name', 'Acme');

		$this->assertSame('Acme', Option::get('mail_from_name'));
		$this->assertSame('Acme', apply_filters('wp_mail_from_name', 'WordPress'));
	}

	public function testMailSendingDefault(): void
	{
		$this->assertTrue(Option::get('mail_sending'));
	}

	public function testMailSendingFilter(): void
	{
		$this->assertNull(apply_filters('pre_wp_mail', null));

		$this->assertTrue(Option::update('mail_sending', false));
		$this->assertFalse(Option::get('mail_sending'));

		$this->assertFalse(apply_filters('pre_wp_mail', null));
	}
}
