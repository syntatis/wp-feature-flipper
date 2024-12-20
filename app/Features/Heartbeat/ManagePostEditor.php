<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Heartbeat;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\HasHookName;
use Syntatis\FeatureFlipper\Helpers\Option;

use function is_numeric;

use const PHP_INT_MAX;

class ManagePostEditor implements Hookable
{
	use HasHookName;

	private bool $heartbeat;

	public function __construct()
	{
		$this->heartbeat = (bool) Option::get('heartbeat');
	}

	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_init', [$this, 'deregisterScripts'], PHP_INT_MAX);
		$hook->addFilter('heartbeat_settings', [$this, 'filterSettings'], PHP_INT_MAX);
		$hook->addFilter(
			self::optionName('heartbeat_post_editor'),
			fn ($value) => $this->heartbeat ? $value : false,
		);
		$hook->addFilter(
			self::defaultOptionName('heartbeat_post_editor'),
			fn ($value) => $this->heartbeat ? $value : false,
		);
	}

	public function deregisterScripts(): void
	{
		if (! self::isPostEditor() || (bool) Option::get('heartbeat_post_editor')) {
			return;
		}

		wp_deregister_script('heartbeat');
	}

	/**
	 * @param array<string,mixed> $settings
	 *
	 * @return array<string,mixed>
	 */
	public function filterSettings(array $settings): array
	{
		// If it's not a post edit screen, return the settings as is.
		if (! is_admin() || ! self::isPostEditor()) {
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
		$pagenow = $GLOBALS['pagenow'] ?? '';

		return is_admin() && ($pagenow === 'post.php' || $pagenow === 'post-new.php');
	}
}
