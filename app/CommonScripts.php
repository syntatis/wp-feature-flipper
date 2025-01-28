<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;

final class CommonScripts implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_enqueue_scripts', [$this, 'enqueueScripts']);
		$hook->addAction('wp_enqueue_scripts', [$this, 'enqueueScripts']);
	}

	public function enqueueScripts(): void
	{
		if (! is_user_logged_in()) {
			return;
		}

		wp_enqueue_style(App::name() . '-common', App::url('dist/assets/index.css'));
	}
}
