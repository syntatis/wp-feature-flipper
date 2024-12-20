<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Symfony\Component\Uid\Uuid;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Query;

use function is_string;

class Media implements Hookable
{
	public function hook(Hook $hook): void
	{
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

		if (! (bool) Option::get('attachment_slug')) {
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

		$hook->addFilter(
			'media_library_infinite_scrolling',
			static fn (): bool => (bool) Option::get('media_infinite_scroll'),
		);

		$hook->addFilter(
			'jpeg_quality',
			static function ($quality) {
				if (! (bool) Option::get('jpeg_compression')) {
					return 100;
				}

				return Option::get('jpeg_compression_quality');
			},
			99,
		);
	}

	public function toNotFound(): void
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
