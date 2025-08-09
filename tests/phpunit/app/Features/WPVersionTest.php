<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\WPVersion;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Modules\Site;
use Syntatis\Tests\WPTestCase;

use function iterator_to_array;

class WPVersionTest extends WPTestCase
{
	private WPVersion $instance;
	private Hook $hook;

	// phpcs:ignore
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new WPVersion();
		$this->instance->hook($this->hook);
	}

	public function testReturnedInstanceWhenEnabled(): void
	{
		$containerMock = $this->createMock(ContainerInterface::class);
		$instances = iterator_to_array((new Site())->getInstances($containerMock));
		$instance = $instances['wp_version'] ?? null;

		$this->assertNull($instance);
	}

	public function testReturnedInstanceWhenDisabled(): void
	{
		Option::update('wp_version', false);

		$containerMock = $this->createMock(ContainerInterface::class);
		$instances = iterator_to_array((new Site())->getInstances($containerMock));
		$instance = $instances['wp_version'] ?? null;

		$this->assertInstanceOf(WPVersion::class, $instance);
	}

	/** @dataProvider dataRemoveVersionFromArg */
	public function testRemoveVersionFromArg(string $src, string $expected): void
	{
		$result = WPVersion::removeVersionFromArg($src);

		$this->assertEquals($expected, $result);
	}

	public static function dataRemoveVersionFromArg(): iterable
	{
		yield ['https://example.com/script.js?v=12&ver=6.0.0', 'https://example.com/script.js?v=12'];
		yield ['https://example.com/script.js?ver=6.0.0&v=12', 'https://example.com/script.js?v=12'];
		yield ['https://example.com/script.js?ver=5.8.1', 'https://example.com/script.js'];
		yield ['https://example.com/style.css?ver=1.0.0', 'https://example.com/style.css'];
		yield ['https://example.com/script.js?ver=', 'https://example.com/script.js'];
		yield ['https://example.com/image.png', 'https://example.com/image.png'];
		yield ['https://example.com/script.js', 'https://example.com/script.js'];
	}
}
