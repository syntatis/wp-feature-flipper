<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\ObfuscateUsernames;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;

/**
 * @group feature-obfuscate-usernames
 * @group module-security
 */
class ObfuscateUsernamesTest extends WPTestCase
{
	private Hook $hook;
	private ObfuscateUsernames $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new ObfuscateUsernames();
		$this->instance->hook($this->hook);
	}

	/** @testdox should has the callback attached to hook */
	public function tesHook(): void
	{
		$this->assertFalse($this->hook->hasAction('pre_get_posts', [$this->instance, 'preGetPosts']));
		$this->assertFalse($this->hook->hasFilter('author_link', [$this->instance, 'filterAuthorLink']));
		$this->assertFalse($this->hook->hasFilter('insert_custom_user_meta', [$this->instance, 'filterInsertCustomUserMeta']));
		$this->assertFalse($this->hook->hasFilter('rest_prepare_user', [$this->instance, 'filterRestPrepareUser']));

		Option::update('obfuscate_usernames', true);
		$this->instance->hook($this->hook);

		$this->assertSame(PHP_INT_MAX, $this->hook->hasAction('pre_get_posts', [$this->instance, 'preGetPosts']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('author_link', [$this->instance, 'filterAuthorLink']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('insert_custom_user_meta', [$this->instance, 'filterInsertCustomUserMeta']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('rest_prepare_user', [$this->instance, 'filterRestPrepareUser']));
	}

	/** @testdox should return default value */
	public function testOptionDefault(): void
	{
		$this->assertFalse(Option::get('obfuscate_usernames'));
	}

	/** @testdox should return updated value */
	public function testOptionUpdate(): void
	{
		$this->assertTrue(Option::update('obfuscate_usernames', true));
		$this->assertTrue(Option::get('obfuscate_usernames'));
	}
}
