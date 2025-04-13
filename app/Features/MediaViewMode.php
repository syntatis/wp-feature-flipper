<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Screen;

final class MediaViewMode implements Hookable
{
	public function hook(Hook $hook): void
	{
		$mode = Option::get('media_view_mode');

		if ($mode === 'both') {
			return;
		}

		/**
		 * Force the media library to use the specified view mode.
		 */
		$_GET['mode'] = $mode;
		$hook->addAction('admin_enqueue_scripts', [$this, 'addInlineScript']);
	}

	public function addInlineScript(): void
	{
		$screen = get_current_screen();

		if (! $screen instanceof WP_Screen || $screen->id !== 'upload' || ! is_admin()) {
			return;
		}

		wp_add_inline_style(
			'media',
			<<<'CSS'
			.wp-filter .view-switch,
			.media-toolbar .view-switch {
				display: none !important;
			}

			.wp-filter .filter-items {
				padding: 12px 0 !important;
			}

			.media-toolbar-secondary {
				padding: 12px 0 !important;
				margin: 0 8px 0 2px !important;
			}
			CSS,
		);
	}
}
