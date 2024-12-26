<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithURI;
use Syntatis\FeatureFlipper\Helpers\Option;

use function defined;
use function printf;

use const PHP_INT_MIN;

class SitePrivate implements Hookable
{
	use WithURI;

	public function hook(Hook $hook): void
	{
		if (! (bool) Option::get('site_private')) {
			return;
		}

		$hook->addAction('template_redirect', [$this, 'forceLogin'], PHP_INT_MIN);
		$hook->addAction('rightnow_end', [$this, 'showRightNowStatus'], PHP_INT_MIN);
		$hook->addFilter('login_site_html_link', static fn (): string => '');
	}

	public function forceLogin(): void
	{
		if (
			is_user_logged_in() ||
			wp_doing_ajax() ||
			wp_doing_cron() ||
			( defined('WP_CLI') && WP_CLI )
		) {
			return;
		}

		if (self::isLoginUrl()) {
			return;
		}

		nocache_headers();
		wp_safe_redirect(wp_login_url(self::getCurrentUrl()), 302);
		exit;
	}

	public function showRightNowStatus(): void
	{
		printf(
			<<<'HTML'
			<div style="color: var(--kubrick-gray-500);">
				<span class="dashicons dashicons-warning"></span>
				<a href="%s">%s</a>
			</div>
			HTML,
			esc_url(admin_url('options-general.php?page=' . App::name() . '&tab=site')),
			esc_html(__('The site is currently in private mode', 'syntatis-feature-flipper')),
		);
	}
}
