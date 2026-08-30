<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use const PHP_INT_MAX;

/**
 * Manage the WordPress autosave feature.
 *
 * WordPress automatically saves posts and pages while they are being edited.
 * The classic editor does this through the `autosave` script, while the block
 * editor schedules it from the `autosaveInterval` editor setting. Switching the
 * feature off disables both.
 */
final class Autosave implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addAction('init', [$this, 'deregisterScripts'], PHP_INT_MAX);
		$hook->addFilter('block_editor_settings_all', [$this, 'filterBlockEditorSettings'], PHP_INT_MAX);
	}

	/**
	 * Deregister the script used by the classic editor to autosave posts.
	 */
	public function deregisterScripts(): void
	{
		if (Option::isOn('autosave')) {
			return;
		}

		wp_deregister_script('autosave');
	}

	/**
	 * Disable the block editor autosave when the feature is switched off.
	 *
	 * @param array<string,mixed> $settings The block editor settings.
	 *
	 * @return array<string,mixed>
	 */
	public function filterBlockEditorSettings(array $settings): array
	{
		if (Option::isOn('autosave')) {
			return $settings;
		}

		$settings['autosaveInterval'] = 0;

		return $settings;
	}
}
