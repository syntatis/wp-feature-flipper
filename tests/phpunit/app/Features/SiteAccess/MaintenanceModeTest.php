<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features\SiteAccess;

use SSFV\Codex\Foundation\Hooks\Hook;
use stdClass;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Features\SiteAccess\MaintenanceMode;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * @group feature-site-access
 * @group module-site
 */
class MaintenanceModeTest extends WPTestCase
{
	use WithHookName;

	private MaintenanceMode $instance;
	private Hook $hook;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new MaintenanceMode();
		$this->instance->hook($this->hook);
	}

	/** @testdox should has the callback attached to hook */
	public function testHook(): void
	{
		$this->assertSame(
			PHP_INT_MAX,
			$this->hook->hasFilter(self::sanitizeOptionHook('site_maintenance_args'), [$this->instance, 'sanitizeArgsOption']),
		);
		$this->assertFalse($this->hook->hasAction('admin_bar_menu', [$this->instance, 'adminBarMenu']));
		$this->assertFalse($this->hook->hasAction('rightnow_end', [$this->instance, 'showRightNowStatus']));
		$this->assertFalse($this->hook->hasAction('template_redirect', [$this->instance, 'forceMaintenance']));
		$this->assertFalse($this->hook->hasFilter('wp_title', [$this->instance, 'filterPageTitle']));

		Option::update('site_access', 'maintenance');
		$this->instance->hook($this->hook);

		$this->assertSame(
			PHP_INT_MAX,
			$this->hook->hasFilter(self::sanitizeOptionHook('site_maintenance_args'), [$this->instance, 'sanitizeArgsOption']),
		);
		$this->assertSame(
			PHP_INT_MIN,
			$this->hook->hasAction('admin_bar_menu', [$this->instance, 'adminBarMenu']),
		);
		$this->assertSame(
			PHP_INT_MIN,
			$this->hook->hasAction('rightnow_end', [$this->instance, 'showRightNowStatus']),
		);
		$this->assertSame(
			PHP_INT_MIN,
			$this->hook->hasAction('template_redirect', [$this->instance, 'templateRedirect']),
		);
		$this->assertSame(
			PHP_INT_MAX,
			$this->hook->hasFilter('wp_title', [$this->instance, 'filterPageTitle']),
		);
	}

	/**
	 * @dataProvider dataSanitizeArgsOption
	 *
	 * @param mixed               $value    The value to sanitize.
	 * @param array<string,mixed> $expected The exected returned value.
	 */
	public function testSanitizeArgsOption($value, array $expected): void
	{
		$this->assertSame($expected, $this->instance->sanitizeArgsOption($value));
	}

	public static function dataSanitizeArgsOption(): iterable
	{
		yield [
			new stdClass(),
			[
				'headline' => '',
				'description' => '',
			],
		];

		yield [
			[],
			[
				'headline' => '',
				'description' => '',
			],
		];

		yield [
			['foo' => 'bar'],
			[
				'headline' => '',
				'description' => '',
			],
		];

		yield [
			['headline' => '<div>Hello</div> <foo>World</foo>'],
			[
				'headline' => 'Hello World',
				'description' => '',
			],
		];

		yield [
			[
				'headline' => '<div>Hello</div> <foo>World</foo>',
				'description' => '<p>This is a description</p>',
			],
			[
				'headline' => 'Hello World',
				'description' => 'This is a description',
			],
		];
	}
}
