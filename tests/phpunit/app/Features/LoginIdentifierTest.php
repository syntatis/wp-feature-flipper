<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\LoginIdentifier;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;

/**
 * @group feature-login-identifier
 * @group module-security
 */
class LoginIdentifierTest extends WPTestCase
{
	private Hook $hook;
	private LoginIdentifier $instance;

	/** @var array<string,mixed> */
	private array $servers;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->servers = $_SERVER;
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		unset($_SERVER['SCRIPT_NAME']);

		$_SERVER = $this->servers;

		parent::tear_down();
	}

	/** @testdox should has the callback attached to hook */
	public function testHook(): void
	{
		$instance = new LoginIdentifier();
		$instance->hook($this->hook);

		$this->assertFalse($this->hook->hasFilter('gettext', [$instance, 'filterGetText']));
		$this->assertSame(20, $this->hook->hasAction('authenticate', 'wp_authenticate_username_password'));
		$this->assertSame(20, $this->hook->hasAction('authenticate', 'wp_authenticate_email_password'));
	}

	/** @testdox should remove the username authentication */
	public function testHookEmailAuthentication(): void
	{
		update_option(Option::name('login_identifier'), 'email');

		$instance = new LoginIdentifier();
		$instance->hook($this->hook);

		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('gettext', [$instance, 'filterGetText']));
		$this->assertFalse($this->hook->hasAction('authenticate', 'wp_authenticate_username_password'));
		$this->assertSame(20, $this->hook->hasAction('authenticate', 'wp_authenticate_email_password'));
	}

	/** @testdox should remove the email authentication */
	public function testHookUsernameAuthentication(): void
	{
		update_option(Option::name('login_identifier'), 'username');

		$instance = new LoginIdentifier();
		$instance->hook($this->hook);

		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('gettext', [$instance, 'filterGetText']));
		$this->assertSame(20, $this->hook->hasAction('authenticate', 'wp_authenticate_username_password'));
		$this->assertFalse($this->hook->hasAction('authenticate', 'wp_authenticate_email_password'));
	}

	/** @testdox should return the default value */
	public function testOptionsDefault(): void
	{
		$this->assertSame('both', Option::get('login_identifier'));
	}

	/** @testdox should return the updated value */
	public function testOptionsUpdated(): void
	{
		update_option(Option::name('login_identifier'), 'email');

		$this->assertSame('email', Option::get('login_identifier'));

		update_option(Option::name('login_identifier'), 'username');

		$this->assertSame('username', Option::get('login_identifier'));
	}

	/** @testdox should return "Email" label */
	public function testFilterGetTextEmail(): void
	{
		$_SERVER['SCRIPT_NAME'] = '/wp-login.php';

		update_option(Option::name('login_identifier'), 'email');

		$instance = new LoginIdentifier();
		$instance->hook($this->hook);

		$this->assertTrue(is_login());
		$this->assertSame('Email', $instance->filterGetText('Username or Email Address', 'Username or Email Address', 'default'));
	}

	/** @testdox should return "Username" label */
	public function testFilterGetTextUsername(): void
	{
		$_SERVER['SCRIPT_NAME'] = '/wp-login.php';

		update_option(Option::name('login_identifier'), 'username');

		$instance = new LoginIdentifier();
		$instance->hook($this->hook);

		$this->assertTrue(is_login());
		$this->assertSame('Username', $instance->filterGetText('Username or Email Address', 'Username or Email Address', 'default'));
	}

	/** @testdox should return the default label */
	public function testFilterGetTextNotLoginpage(): void
	{
		update_option(Option::name('login_identifier'), 'username');

		$instance = new LoginIdentifier();
		$instance->hook($this->hook);

		$this->assertFalse(is_login());
		$this->assertSame('Username or Email Address', $instance->filterGetText('Username or Email Address', 'Username or Email Address', 'default'));
	}
}
