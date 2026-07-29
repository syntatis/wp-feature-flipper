<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use SFFV\Symfony\Component\Uid\Uuid;
use Throwable;

final class AttachmentSlug implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter('wp_unique_post_slug', [self::class, 'generateUuid'], 99, 4);
	}

	public static function generateUuid(string $slug, string $id, string $status, string $type): string
	{
		if ($type !== 'attachment' || Uuid::isValid($slug)) {
			return $slug;
		}

		try {
			return (string) Uuid::v5(Uuid::fromString(Uuid::NAMESPACE_URL), $slug);
		} catch (Throwable $th) {
			return $slug;
		}
	}
}
