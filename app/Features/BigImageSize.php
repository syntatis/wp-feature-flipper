<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use function intval;
use function is_numeric;

use const PHP_INT_MAX;

/**
 * Configures WordPress's automatic scaling of large uploaded images through
 * the `big_image_size_threshold` filter.
 *
 * Since WordPress 5.3, images wider or taller than the threshold are scaled
 * down on upload. This feature lets site administrators keep the WordPress
 * default, set a custom threshold, or disable the automatic scaling entirely.
 *
 * The feature only configures the existing WordPress behavior. It does not
 * reimplement image processing, and it only affects images processed after
 * the setting is saved.
 *
 * @see https://developer.wordpress.org/reference/hooks/big_image_size_threshold/
 */
final class BigImageSize implements Hookable
{
	/**
	 * The WordPress default threshold, in pixels.
	 */
	public const DEFAULT_THRESHOLD = 2560;

	/**
	 * The minimum allowed custom threshold, in pixels.
	 *
	 * A threshold of zero would disable the scaling, which is handled by the
	 * explicit "Disabled" state instead.
	 */
	public const MIN_THRESHOLD = 1;

	/**
	 * The maximum allowed custom threshold, in pixels.
	 */
	public const MAX_THRESHOLD = 10000;

	public function hook(Hook $hook): void
	{
		$hook->addFilter(
			'big_image_size_threshold',
			[$this, 'filterBigImageSizeThreshold'],
			PHP_INT_MAX,
		);

		/**
		 * Normalize the stored threshold so the value passed to WordPress is
		 * always a valid positive integer, regardless of how it was supplied.
		 */
		$hook->addFilter(
			Option::hook('sanitize:big_image_size_threshold'),
			[$this, 'sanitizeThreshold'],
			PHP_INT_MAX,
			1,
		);
	}

	/**
	 * Filter the "big image" threshold value.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/big_image_size_threshold/
	 *
	 * @param int $threshold The threshold value in pixels. Default 2560.
	 *
	 * @return int|false The filtered threshold value, or `false` to disable the scaling.
	 */
	public function filterBigImageSizeThreshold(int $threshold)
	{
		// When disabled, return `false` so WordPress does not scale large images.
		if (! Option::isOn('big_image_size')) {
			return false;
		}

		$value = Option::get('big_image_size_threshold');

		/**
		 * Fall back to the WordPress default when the stored value is not
		 * numeric. The value is normally normalized by the sanitize callback,
		 * but this guards against values supplied outside of WordPress.
		 */
		if (! is_numeric($value)) {
			return $threshold;
		}

		return intval($value);
	}

	/**
	 * Normalize the stored threshold value.
	 *
	 * Non-numeric, zero, negative, and out-of-range values are normalized to
	 * the WordPress default threshold.
	 *
	 * @param mixed $value The value to sanitize.
	 */
	public function sanitizeThreshold($value): int
	{
		// Non-numeric values cannot be represented as a threshold.
		if (! is_numeric($value)) {
			return self::DEFAULT_THRESHOLD;
		}

		$value = intval($value);

		if ($value < self::MIN_THRESHOLD || $value > self::MAX_THRESHOLD) {
			return self::DEFAULT_THRESHOLD;
		}

		return $value;
	}
}
