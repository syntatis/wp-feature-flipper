<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\Updates;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;

use function define;
use function defined;

use const PHP_INT_MAX;

class ManageGlobal implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('updates')) {
			$hook->addAction('admin_menu', static function (): void {
				remove_submenu_page('index.php', 'update-core.php');
			}, PHP_INT_MAX);
		}

		if ((bool) Option::get('auto_updates')) {
			return;
		}

		if (! defined('AUTOMATIC_UPDATER_DISABLED')) {
			define('AUTOMATIC_UPDATER_DISABLED', false);
		}

		$hook->addFilter('automatic_updater_disabled', '__return_false');
		$hook->addFilter('auto_update_translation', '__return_false');
	}
}
