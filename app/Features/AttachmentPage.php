<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Query;

use function is_string;

final class AttachmentPage implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter(Option::hook('default:attachment_page'), static function (): bool {
			/**
			 * In WordPress 6.4, A new option, `wp_attachment_pages_enabled` is introduced
			 * to control the attachment page behavior.
			 *
			 * - For new sites, the option will be set to `0` by default, and the attachment
			 * page will be disabled.
			 * - For existing sites, the option will be set to `1` after the upgrade and
			 * the attachment page will
			 * remain enabled.
			 *
			 * By default, this plugin will follow this default behavior.
			 *
			 * @see https://make.wordpress.org/core/2023/10/16/changes-to-attachment-pages/
			 */
			$enabled = get_option('wp_attachment_pages_enabled', null);

			return $enabled === '1' || $enabled === null;
		});

		$hook->addAction(
			Option::hook('add:attachment_page'),
			static function ($option, $value): void {
				update_option('wp_attachment_pages_enabled', (bool) $value ? '1' : '0');
			},
			10,
			2,
		);

		$hook->addAction(
			Option::hook('update:attachment_page'),
			static function ($oldValue, $newValue): void {
				update_option('wp_attachment_pages_enabled', (bool) $newValue ? '1' : '0');
			},
			10,
			2,
		);

		if (! Option::isOn('attachment_page') && ! is_admin()) {
			$hook->addAction('template_redirect', static function (): void {
				if (! is_attachment()) {
					return;
				}

				self::notFound();
			});

			$hook->addFilter(
				'redirect_canonical',
				/* @phpstan-ignore shipmonk.missingNativeReturnTypehint */
				static function (string $url) {
					if (! is_attachment()) {
						return $url;
					}

					self::notFound();
				},
			);

			/**
			 * Replace the link to "View Attachment Page" with the actual attachment URL.
			 */
			$hook->addFilter('attachment_link', static function (string $url, int $id): string {
				$attachmentUrl = wp_get_attachment_url($id);

				if (is_string($attachmentUrl)) {
					return $attachmentUrl;
				}

				return $url;
			}, 99, 2);
		}
	}

	private static function notFound(): void
	{
		/** @var WP_Query|null $wpQuery */
		$wpQuery = $GLOBALS['wp_query'] ?? null;

		if ($wpQuery === null) {
			return;
		}

		$wpQuery->set_404();
		status_header(404);
		nocache_headers();
	}
}
