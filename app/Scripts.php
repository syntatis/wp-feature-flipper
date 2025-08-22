<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Facades\App;
use SFFV\Codex\Foundation\Hooks\Hook;

final class Scripts implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_enqueue_scripts', [self::class, 'enqueueScripts']);
		$hook->addAction('wp_enqueue_scripts', [self::class, 'enqueueScripts']);
	}

	public static function enqueueScripts(): void
	{
		if (! is_user_logged_in()) {
			return;
		}

		wp_enqueue_style(App::name() . '-common', App::url('dist/assets/index.css'));
	}
}
