<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\SiteVisibility;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use function header;

use const PHP_INT_MIN;

class MaintenanceMode implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (Option::get('site_access') !== 'maintenance') {
			return;
		}

		$hook->addAction('template_redirect', [$this, 'templateRedirect'], PHP_INT_MIN);
	}

	public function templateRedirect(): void
	{
		status_header(503);

		header('Retry-After: ' . 3600);

		include App::dir('inc/views/maintenance-mode.php');

		exit;
	}
}
