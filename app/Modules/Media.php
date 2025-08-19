<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SFFV\Codex\Contracts\Extendable;
use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use SFFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\AttachmentPage;
use Syntatis\FeatureFlipper\Features\AttachmentSlug;
use Syntatis\FeatureFlipper\Helpers\Option;

final class Media implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter(
			'media_library_infinite_scrolling',
			static fn (): bool => Option::isOn('media_infinite_scroll'),
		);

		$hook->addFilter(
			'big_image_size_threshold',
			static fn () => Option::isOn('big_image_size') ?
				Option::get('big_image_size_threshold') :
				false,
		);

		$hook->addFilter(
			'jpeg_quality',
			static fn ($quality) => Option::isOn('jpeg_compression') ?
				Option::get('jpeg_compression_quality') :
				100,
		);
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield 'attachment_page' => new AttachmentPage();
		yield 'attachment_slug' => ! Option::isOn('attachment_slug') ? new AttachmentSlug() : null;
	}
}
