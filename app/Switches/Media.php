<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Switches;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Symfony\Component\Uid\Uuid;
use Syntatis\FeatureFlipper\Option;
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

					return (string) Uuid::v4();
				},
				99,
				4,
			);
		}

		$hook->addFilter(
			'jpeg_quality',
			static function ($quality, string $context) {
				if ((bool) Option::get('jpeg_compression')) {
					return Option::get('jpeg_compression_quality');
				}

				return 100;
			},
			99,
			2,
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
