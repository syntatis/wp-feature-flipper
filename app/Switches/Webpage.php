<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Switches;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;

class Webpage implements Hookable
{
	public function hook(Hook $hook): void
	{
		// 1. RSD Link.
		if (! Option::get('rsd_link')) {
			$hook->removeAction('wp_head', 'rsd_link');
		}

		// 2. WordPress Generator meta tag.
		if (! Option::get('generator_tag')) {
			$hook->removeAction('wp_head', 'wp_generator');
		}

		// 3. Shortlink.
		if (Option::get('shortlink')) {
			return;
		}

		$hook->removeAction('wp_head', 'wp_shortlink_wp_head');
		$hook->removeAction('wp_head', 'wp_shortlink_header');
	}
}
