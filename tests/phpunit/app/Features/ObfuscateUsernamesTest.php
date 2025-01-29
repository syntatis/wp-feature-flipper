<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\ObfuscateUsernames;
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
}
