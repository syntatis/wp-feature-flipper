<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use ArrayAccess;
use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Contracts\InlineDataProvider;
use Syntatis\FeatureFlipper\Helpers\Option;

use function array_merge;
use function ctype_digit;
use function define;
use function defined;
use function is_array;
use function is_int;
use function is_string;
use function trim;

/**
 * Manage the WordPress Trash retention period.
 */
final class TrashRetention implements Hookable, InlineDataProvider
{
	/** The number of days WordPress keeps trashed content by default. */
	private const DEFAULT_DAYS = 30;

	/** The maximum number of days that can be configured. */
	private const MAX_DAYS = 3650;

	/** Whether `EMPTY_TRASH_DAYS` was already defined before this plugin. */
	private static bool $wasPredefined = false;

	public function hook(Hook $hook): void
	{
		$hook->addFilter(Option::hook('sanitize:trash_retention'), [$this, 'sanitize'], 10, 1);

		/**
		 * Check if `EMPTY_TRASH_DAYS` is already defined before this plugin.
		 */
		if (defined('EMPTY_TRASH_DAYS')) {
			self::$wasPredefined = true;

			return;
		}

		define('EMPTY_TRASH_DAYS', self::retentionDays());
	}

	/**
	 * Whether `EMPTY_TRASH_DAYS` is managed outside the plugin (e.g. `wp-config.php`).
	 */
	public static function hasExplicitConfig(): bool
	{
		return self::$wasPredefined;
	}

	/**
	 * Retrieve the effective retention period in days.
	 */
	public static function effectiveDays(): int
	{
		if (self::$wasPredefined && defined('EMPTY_TRASH_DAYS')) {
			return EMPTY_TRASH_DAYS;
		}

		return self::retentionDays();
	}

	/**
	 * Provide the Trash retention values to the plugin's global inline data.
	 *
	 * @phpstan-param ArrayAccess<string,mixed> $data
	 *
	 * @phpstan-return ArrayAccess<string,mixed>
	 */
	public function inlineData(ArrayAccess $data): ArrayAccess
	{
		$tab = $_GET['tab'] ?? null;

		if ($tab !== null && $tab !== 'general') {
			return $data;
		}

		$features = $data['features'] ?? [];
		$data['features'] = array_merge(
			is_array($features) ? $features : [],
			[
				'trashRetention' => [
					'isLocked' => self::hasExplicitConfig(),
					'days' => self::effectiveDays(),
					'defaultDays' => self::DEFAULT_DAYS,
					'maxDays' => self::MAX_DAYS,
				],
			],
		);

		return $data;
	}

	/**
	 * Retrieve the number of days to keep trashed content, from the plugin option.
	 */
	public static function retentionDays(): int
	{
		return self::normalize(Option::get('trash_retention')) ?? self::DEFAULT_DAYS;
	}

	/**
	 * Sanitize the Trash Retention option value.
	 *
	 * @param mixed $value The value to sanitize.
	 *
	 * @return int The normalized number of days.
	 */
	public function sanitize($value): int
	{
		return self::normalize($value) ?? self::DEFAULT_DAYS;
	}

	/**
	 * Normalize a retention value into a valid number of days.
	 *
	 * @param mixed $value The value to normalize.
	 *
	 * @return int|null The normalized days, or null when the value is invalid.
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
			$days = (int) $value;

			return self::isValid($days) ? $days : null;
		}

		return null;
	}

	private static function isValid(int $days): bool
	{
		return $days >= 0 && $days <= self::MAX_DAYS;
	}
}
