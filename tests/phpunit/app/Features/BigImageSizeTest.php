<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\BigImageSize;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\Tests\WPTestCase;

use function apply_filters;
use function wp_get_attachment_metadata;
use function wp_update_attachment_metadata;

use const PHP_INT_MAX;

/**
 * @group feature-big-image-size
 * @group module-media
 */
class BigImageSizeTest extends WPTestCase
{
	private Hook $hook;
	private BigImageSize $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new BigImageSize();
		$this->instance->hook($this->hook);
	}

	/** @testdox should have callback attached to hooks */
	public function testHook(): void
	{
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter('big_image_size_threshold', [$this->instance, 'filterBigImageSizeThreshold']));
		$this->assertSame(PHP_INT_MAX, $this->hook->hasFilter(Option::hook('sanitize:big_image_size_threshold'), [$this->instance, 'sanitizeThreshold']));
	}

	/** @testdox should use the WordPress default threshold by default */
	public function testDefaultThreshold(): void
	{
		$this->assertTrue(Option::isOn('big_image_size'));
		$this->assertSame(2560, Option::get('big_image_size_threshold'));
		$this->assertSame(2560, apply_filters('big_image_size_threshold', 2560, [], '', 0));
	}

	/** @testdox should not interfere with the WordPress default behavior */
	public function testDefaultMatchesWordPressBehavior(): void
	{
		$this->assertSame(2560, apply_filters('big_image_size_threshold', 2560, [4000, 3000], '/tmp/image.jpg', 1));
	}

	/** @testdox should pass a custom threshold to WordPress */
	public function testCustomThreshold(): void
	{
		Option::update('big_image_size_threshold', 1920);

		$this->assertSame(1920, apply_filters('big_image_size_threshold', 2560, [], '', 0));
	}

	/** @testdox should not be affected by the image arguments passed by WordPress */
	public function testFilterIgnoresImageArguments(): void
	{
		Option::update('big_image_size_threshold', 1280);

		$this->assertSame(1280, apply_filters('big_image_size_threshold', 2560, [4000, 3000, 'mime' => 'image/jpeg'], '/tmp/image.jpg', 12));
	}

	/** @testdox should disable the threshold when the feature is switched off */
	public function testDisabled(): void
	{
		Option::update('big_image_size', false);

		$this->assertFalse(Option::isOn('big_image_size'));
		$this->assertFalse(apply_filters('big_image_size_threshold', 2560, [], '', 0));
	}

	/** @testdox should apply the threshold repeatedly without side effects */
	public function testIdempotent(): void
	{
		Option::update('big_image_size_threshold', 1920);

		for ($i = 0; $i < 3; $i++) {
			$this->assertSame(1920, apply_filters('big_image_size_threshold', 2560, [], '', 0));
		}

		$this->assertSame(1920, Option::get('big_image_size_threshold'));
	}

	/** @testdox should not modify existing attachments when the setting changes */
	public function testExistingImagesUnaffected(): void
	{
		$attachmentId = self::factory()->attachment->create(['post_mime_type' => 'image/jpeg']);

		$metadata = [
			'width' => 3000,
			'height' => 2000,
			'file' => '2026/08/large.jpg',
			'sizes' => [
				'thumbnail' => ['file' => 'large-150x150.jpg', 'width' => 150, 'height' => 150, 'mime-type' => 'image/jpeg'],
				'medium' => ['file' => 'large-300x200.jpg', 'width' => 300, 'height' => 200, 'mime-type' => 'image/jpeg'],
			],
		];

		wp_update_attachment_metadata($attachmentId, $metadata);

		$generations = 0;
		add_filter('wp_generate_attachment_metadata', static function ($data) use (&$generations) {
			$generations++;

			return $data;
		});

		// Changing the settings must not re-process or resize existing images.
		Option::update('big_image_size', false);
		Option::update('big_image_size_threshold', 1920);

		$this->assertSame(0, $generations);
		$this->assertSame($metadata, wp_get_attachment_metadata($attachmentId));
	}

	/**
	 * @dataProvider dataSanitizeThreshold
	 * @testdox should sanitize the threshold value
	 *
	 * @param mixed $value    The value to sanitize.
	 * @param int   $expected The expected sanitized value.
	 */
	public function testSanitizeThreshold($value, int $expected): void
	{
		$this->assertSame($expected, $this->instance->sanitizeThreshold($value));
	}

	public static function dataSanitizeThreshold(): iterable
	{
		yield 'default' => [2560, 2560];
		yield 'custom valid' => [1280, 1280];
		yield 'boundary minimum' => [1, 1];
		yield 'boundary maximum' => [10000, 10000];
		yield 'decimal truncated' => [1280.5, 1280];
		yield 'decimal string truncated' => ['1280.5', 1280];
		yield 'zero' => [0, 2560];
		yield 'negative' => [-5, 2560];
		yield 'above maximum' => [10001, 2560];
		yield 'non numeric' => ['abc', 2560];
		yield 'empty string' => ['', 2560];
		yield 'null' => [null, 2560];
	}
}
