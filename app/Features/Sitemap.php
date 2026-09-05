<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use ArrayAccess;
use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use function array_merge;
use function is_array;

use const PHP_INT_MAX;

/**
 * Controls the built-in XML sitemap in WordPress.
 */
final class Sitemap implements Hookable
{
	public function hook(Hook $hook): void
	{
		/**
		 * The sitemap may already be disabled by third-party plugins, such as an
		 * SEO plugin that ship their own sitemap. As they've already done the
		 * work to disable the built-in sitemap, we should not re-enable it.
		 *
		 * Instead, we should respect their decision and allow them to manage the
		 * sitemap state
		 */
		$hook->addFilter(
			'wp_sitemaps_enabled',
			static fn (bool $enabled): bool => $enabled && Option::isOn('sitemap'),
			PHP_INT_MAX,
		);

		$hook->addFilter('syntatis/feature_flipper/inline_data', [$this, 'filterInlineData']);
	}

	/**
	 * Provide the Sitemap state to the plugin's global inline data.
	 *
	 * The built-in sitemap may be disabled by a third-party plugin (e.g., an
	 * SEO plugin that ships its own sitemap). Here we expose the state so
	 * the UI can hide the "Sitemap" toggle, which would have no effect
	 * in that case.
	 *
	 * @phpstan-param ArrayAccess<string,mixed> $data
	 *
	 * @phpstan-return ArrayAccess<string,mixed>
	 */
	public function filterInlineData(ArrayAccess $data): ArrayAccess
	{
		$tab = $_GET['tab'] ?? null;

		if ($tab !== null && $tab !== 'site') {
			return $data;
		}

		$features = $data['features'] ?? [];
		$isEnabled = (bool) apply_filters('wp_sitemaps_enabled', true);
		$isManagedByOthers = Option::isOn('sitemap') && ! $isEnabled;

		$data['features'] = array_merge(
			is_array($features) ? $features : [],
			['sitemap' => ['isManagedByOthers' => $isManagedByOthers]],
		);

		return $data;
	}
}
