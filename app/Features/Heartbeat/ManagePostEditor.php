<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Heartbeat;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Admin;
use Syntatis\FeatureFlipper\Helpers\Option;

use function is_numeric;

use const PHP_INT_MAX;

final class ManagePostEditor implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_init', [$this, 'deregisterScripts'], PHP_INT_MAX);
		$hook->addFilter('heartbeat_settings', [$this, 'filterSettings'], PHP_INT_MAX);

		/**
		 * Filter the Heartbeat settings for the post editor screen.
		 *
		 * When Heartbeat is disabled from the global option, it should also disable
		 * the "heartbeat_post_editor" option.
		 */
		$hook->addFilter(
			Option::hook('heartbeat_post_editor'),
			static fn ($value) => Option::isOn('heartbeat') ? $value : false,
		);
		$hook->addFilter(
			Option::hook('default:heartbeat_post_editor'),
			static fn ($value) => Option::isOn('heartbeat') ? $value : false,
		);
	}

	/**
	 * Deregister the Heartbeat script in the post editor screen.
	 */
	public function deregisterScripts(): void
	{
		if (! self::isPostEditor() || Option::isOn('heartbeat_post_editor')) {
			return;
		}

		wp_deregister_script('heartbeat');
	}

	/**
	 * Filter the Heartbeat settings for the post editor area.
	 *
	 * @param array<string,mixed> $settings
	 *
	 * @return array<string,mixed>
	 */
	public function filterSettings(array $settings): array
	{
		if (
			! self::isPostEditor() ||
			! Option::isOn('heartbeat_post_editor')
		) {
			return $settings;
		}

		$interval = Option::get('heartbeat_post_editor_interval');

		if (is_numeric($interval)) {
			$interval = absint($interval);

			$settings['interval'] = $interval;
			$settings['minimalInterval'] = $interval;
		}

		return $settings;
	}

	private static function isPostEditor(): bool
	{
		return Admin::isScreen('post.php') || Admin::isScreen('post-new.php');
	}
}
