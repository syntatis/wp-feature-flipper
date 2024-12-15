<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use function defined;
use function is_string;
use function sprintf;
use function trim;

use const PHP_INT_MIN;

class SitePrivate implements Hookable
{
	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('site_private')) {
			return;
		}

		$hook->addAction('template_redirect', [$this, 'forceLogin'], PHP_INT_MIN);
		$hook->addFilter('login_site_html_link', static fn (): string => '');
	}

	public function forceLogin(): void
	{
		if (
			( defined('DOING_AJAX') && DOING_AJAX ) ||
			( defined('DOING_CRON') && DOING_CRON ) ||
			( defined('WP_CLI') && WP_CLI )
		) {
			return;
		}

		if (is_user_logged_in()) {
			return;
		}

		$schema = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
		$host = isset($_SERVER['HTTP_HOST']) && is_string($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		$uri = isset($_SERVER['REQUEST_URI']) && is_string($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
		$url = trim($host) !== '' ? sprintf('%s%s%s', $schema, $host, $uri) : '';

		nocache_headers();
		wp_safe_redirect(wp_login_url($url), 302);
		exit;
	}
}
