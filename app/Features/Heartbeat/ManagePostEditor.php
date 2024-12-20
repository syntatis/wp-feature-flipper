<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Heartbeat;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\HasHookName;
use Syntatis\FeatureFlipper\Helpers\Option;

use function is_int;

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
		$hook->addFilter('heartbeat_settings', [$this, 'getSettings'], PHP_INT_MAX);

		// Manage the "heartbeat_post_editor" and "heartbeat_post_editor_interval" options.
		$hook->addFilter(
			self::optionName('heartbeat_post_editor'),
			fn ($value) => $this->heartbeat ? $value : $this->heartbeat,
		);
		$hook->addFilter(
			self::defaultOptionName('heartbeat_post_editor'),
			fn ($value) => $this->heartbeat ? $value : $this->heartbeat,
		);
		$hook->addFilter(
			self::optionName('heartbeat_post_editor_interval'),
			fn ($value) => (bool) Option::get('heartbeat_post_editor') && $this->heartbeat ? $value : null,
		);
		$hook->addFilter(
			self::defaultOptionName('heartbeat_post_editor_interval'),
			fn ($value) => (bool) Option::get('heartbeat_post_editor') && $this->heartbeat ? $value : null,
		);
	}

	public function deregisterScripts(): void
	{
		if (! self::isPostEditor()) {
			return;
		}

		if ((bool) Option::get('heartbeat_post_editor')) {
			return;
		}

		wp_deregister_script('heartbeat');
	}

	/**
	 * @param array<string,mixed> $settings
	 *
	 * @return array<string,mixed>
	 */
	public function getSettings(array $settings): array
	{
		// If it's not a post edit screen, return the settings as is.
		if (! self::isPostEditor()) {
			return $settings;
		}

		$interval = Option::get('heartbeat_post_editor_interval');

		if (is_int($interval)) {
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
