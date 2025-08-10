<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;

use function strpos;

final class WPVersion implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter('script_loader_src', [self::class, 'removeVersionFromArg']);
		$hook->addFilter('style_loader_src', [self::class, 'removeVersionFromArg']);
		$hook->addFilter('the_generator', '__return_empty_string');
		$hook->removeAction('wp_head', 'wp_generator');
	}

	public static function removeVersionFromArg(string $src): string
	{
		if (strpos($src, 'ver=') !== false) {
			$src = remove_query_arg('ver', $src);
		}

		return $src;
	}
}
