<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Symfony\Component\Uid\Uuid;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Query;

use function is_string;

class Attachment implements Hookable
{
	use WithHookName;

	public function hook(Hook $hook): void
	{
		$hook->addFilter(self::defaultOptionHook('attachment_page'), static function () {
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
			self::addOptionHook('attachment_page'),
			static function ($option, $value): void {
				update_option('wp_attachment_pages_enabled', (bool) $value ? '1' : '0');
			},
			10,
			2,
		);
		$hook->addAction(
			self::updateOptionHook('attachment_page'),
			static function ($oldValue, $newValue): void {
				update_option('wp_attachment_pages_enabled', (bool) $newValue ? '1' : '0');
			},
			10,
			2,
		);

		// 1. Attachment Page.
		if (! (bool) Option::get('attachment_page')) {
			$hook->addAction('template_redirect', function (): void {
				if (! is_attachment()) {
					return;
				}

				$this->toNotFound();
			});

			$hook->addFilter(
				'redirect_canonical',
				function (string $url) {
					if (! is_attachment()) {
						return $url;
					}

					$this->toNotFound();
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

		if ((bool) Option::get('attachment_slug')) {
			return;
		}

		$hook->addFilter(
			'wp_unique_post_slug',
			static function (string $slug, string $id, string $status, string $type): string {
				if ($type !== 'attachment' || Uuid::isValid($slug)) {
					return $slug;
				}

				return (string) Uuid::v5(Uuid::fromString(Uuid::NAMESPACE_URL), $slug);
			},
			99,
			4,
		);
	}

	private function toNotFound(): void
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
