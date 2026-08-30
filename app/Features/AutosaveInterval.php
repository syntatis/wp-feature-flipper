<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use ArrayAccess;
use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use function array_merge;
use function constant;
use function ctype_digit;
use function define;
use function defined;
use function is_array;
use function is_int;
use function is_string;
use function trim;

/**
 * Manage the WordPress autosave interval.
 *
 * The autosave feature in WordPress is, by default, enabled to save posts
 * and pages automatically while you're using the Editor. If there is a
 * crash or a browser issue, you won't lose your work. You will still
 * be able to recover your work from the last autosave.
 */
final class AutosaveInterval implements Hookable
{
	/** The number of seconds WordPress waits between autosaves by default. */
	private const DEFAULT_INTERVAL = 60;

	/** The minimum autosave interval, in seconds, that can be configured. */
	private const MIN_INTERVAL = 15;

	/** The maximum autosave interval, in seconds, that can be configured. */
	private const MAX_INTERVAL = 3600;

	/** Whether `AUTOSAVE_INTERVAL` was already defined before this plugin. */
	private static bool $wasPredefined = false;

	public function hook(Hook $hook): void
	{
		$hook->addFilter(Option::hook('sanitize:autosave_interval'), [$this, 'sanitize'], 10, 1);
		$hook->addFilter('syntatis/feature_flipper/inline_data', [$this, 'filterInlineData']);

		/**
		 * Respect an existing `AUTOSAVE_INTERVAL` definition (e.g. from
		 * `wp-config.php`) instead of overriding it.
		 */
		if (defined('AUTOSAVE_INTERVAL')) {
			self::$wasPredefined = true;

			return;
		}

		/**
		 * The interval is only applied when autosave is enabled and a custom
		 * interval is configured. When autosave is switched off, the feature
		 * is disabled through its own hooks instead.
		 */
		if (! Option::isOn('autosave') || ! Option::isOn('autosave_interval_enabled')) {
			return;
		}

		define('AUTOSAVE_INTERVAL', self::effectiveInterval());
	}

	/**
	 * Whether `AUTOSAVE_INTERVAL` is managed outside the plugin (e.g. `wp-config.php`).
	 */
	public static function hasExplicitConfig(): bool
	{
		return self::$wasPredefined;
	}

	/**
	 * Retrieve the effective autosave interval, in seconds.
	 */
	public static function effectiveInterval(): int
	{
		if (self::$wasPredefined && defined('AUTOSAVE_INTERVAL')) {
			$interval = constant('AUTOSAVE_INTERVAL');

			return is_int($interval) ? $interval : self::DEFAULT_INTERVAL;
		}

		if (! Option::isOn('autosave_interval_enabled')) {
			return self::DEFAULT_INTERVAL;
		}

		return self::normalize(Option::get('autosave_interval')) ?? self::DEFAULT_INTERVAL;
	}

	/**
	 * Provide the Autosave Interval values to the plugin's global inline data.
	 *
	 * @phpstan-param ArrayAccess<string,mixed> $data
	 *
	 * @phpstan-return ArrayAccess<string,mixed>
	 */
	public function filterInlineData(ArrayAccess $data): ArrayAccess
	{
		$tab = $_GET['tab'] ?? null;

		if ($tab !== null && $tab !== 'general') {
			return $data;
		}

		$features = $data['features'] ?? [];
		$data['features'] = array_merge(
			is_array($features) ? $features : [],
			[
				'autosaveInterval' => [
					'isLocked' => self::hasExplicitConfig(),
					'defaultInterval' => self::DEFAULT_INTERVAL,
					'minInterval' => self::MIN_INTERVAL,
					'maxInterval' => self::MAX_INTERVAL,
				],
			],
		);

		return $data;
	}

	/**
	 * Sanitize the Autosave Interval option value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return int The normalized autosave interval, in seconds.
	 */
	public function sanitize($value): int
	{
		return self::normalize($value) ?? self::DEFAULT_INTERVAL;
	}

	/**
	 * Normalize a value into a valid autosave interval, in seconds.
	 *
	 * @param mixed $value The value to normalize.
	 *
	 * @return int|null The normalized interval, or null when the value is invalid.
	 */
	private static function normalize($value): ?int
	{
		if (is_string($value)) {
			$value = trim($value);
		}

		if (is_int($value)) {
			return self::isValid($value) ? $value : null;
		}

		if (is_string($value) && $value !== '' && ctype_digit($value)) {
			$interval = (int) $value;

			return self::isValid($interval) ? $interval : null;
		}

		return null;
	}

	private static function isValid(int $interval): bool
	{
		return $interval >= self::MIN_INTERVAL && $interval <= self::MAX_INTERVAL;
	}
}
