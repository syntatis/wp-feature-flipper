<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\URL;

use function function_exists;
use function trim;

final class PasswordReset implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addAction('wp', [self::class, 'redirect']);
		$hook->addFilter('allow_password_reset', '__return_false');
		$hook->addFilter('gettext', [self::class, 'removeLostPasswordText']);
	}

	public static function removeLostPasswordText(string $text): string
	{
		if ($text === 'Lost your password?') {
			$text = '';
		}

		return $text;
	}

	public static function redirect(): void
	{
		if (is_user_logged_in()) {
			return;
		}

		if (
			did_action('woocommerce_loaded') === 1 &&
			function_exists('is_wc_endpoint_url') &&
			is_wc_endpoint_url('lost-password')
		) {
			$accountPage = trim(wc_get_page_permalink('myaccount'));

			if ($accountPage !== '') {
				wp_safe_redirect($accountPage, 302);
				exit;
			}

			wp_safe_redirect(home_url(), 302);
			exit;
		}

		if (URL::isLogin() && ($_GET['action'] ?? '') === 'lostpassword') {
			wp_safe_redirect(wp_login_url(), 302);
			exit;
		}
	}
}
