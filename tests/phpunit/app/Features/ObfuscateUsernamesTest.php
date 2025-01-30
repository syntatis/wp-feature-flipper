<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\ObfuscateUsernames;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

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
