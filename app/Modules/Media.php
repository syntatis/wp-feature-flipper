<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Features\Attachment;
use Syntatis\FeatureFlipper\Helpers\Option;

class Media implements Hookable, Extendable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter(
			'media_library_infinite_scrolling',
			static fn (): bool => Option::isOn('media_infinite_scroll'),
		);

		$hook->addFilter(
			'jpeg_quality',
			static function ($quality) {
				if (! Option::isOn('jpeg_compression')) {
					return 100;
				}

				return Option::get('jpeg_compression_quality');
			},
			99,
		);
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new Attachment();
	}
}
