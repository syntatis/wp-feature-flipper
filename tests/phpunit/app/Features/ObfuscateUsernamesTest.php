<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Symfony\Component\Uid\Uuid;
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

	/** @testdox should not generate uuid when it's not enabled */
	public function testNotGenerateUuid(): void
	{
		$user = self::factory()->user->create_and_get();

		$this->assertFalse(Option::get('obfuscate_usernames'));
		$this->assertEmpty(get_user_meta($user->ID, '_syntatis_uuid', true));
	}

	/** @testdox should generate uuid when it's enabled */
	public function testGenerateUuid(): void
	{
		$user = self::factory()->user->create_and_get();

		$this->assertEmpty(get_user_meta($user->ID, '_syntatis_uuid', true));
		$this->assertTrue(Option::update('obfuscate_usernames', true));
		$this->assertTrue(Option::get('obfuscate_usernames'));
		$this->assertTrue(Uuid::isValid(get_user_meta($user->ID, '_syntatis_uuid', true)));
	}

	/** @testdox should generate uuid on new user */
	public function testGenerateUuidOnNewUser(): void
	{
		$this->assertTrue(Option::update('obfuscate_usernames', true));
		$this->assertTrue(Option::get('obfuscate_usernames'));

		// Reload.
		$this->instance->hook($this->hook);
		$user = self::factory()->user->create_and_get();

		$this->assertTrue(Uuid::isValid(get_user_meta($user->ID, '_syntatis_uuid', true)));
	}

	/**
	 * The plugin use the user ID, email, and username to generate a unique UUID.
	 * Users can change their email. When they do, it should not regenerate new
	 * UUID for users.
	 *
	 * @testdox should not regenerate uuid on user update
	 */
	public function testUpdateUserEmail(): void
	{
		$this->assertTrue(Option::update('obfuscate_usernames', true));
		$this->assertTrue(Option::get('obfuscate_usernames'));

		// Reload.
		$this->instance->hook($this->hook);

		$user = self::factory()->user->create_and_get(['user_email' => 'foo@example.org']);
		$uuid = get_user_meta($user->ID, '_syntatis_uuid', true);

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		$this->assertSame('foo@example.org', $user->user_email);
		$this->assertTrue(Uuid::isValid($uuid));

		$user = get_user_by('id', wp_update_user([
			'ID' => $user->ID,
			'user_email' => 'foo2@example.org',
		]));

		// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
		$this->assertSame('foo2@example.org', $user->user_email);
		$this->assertSame($uuid, get_user_meta($user->ID, '_syntatis_uuid', true));
	}
}
