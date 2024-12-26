<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\SiteVisibility;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Concerns\WithURI;
use Syntatis\FeatureFlipper\Helpers\Option;

use function defined;
use function printf;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

/**
 * Handle the site visibility in private mode.
 *
 * Named as `PrivateMode` because "private" a is reserved keyword in PHP.
 */
class PrivateMode implements Hookable
{
	use WithHookName;
	use WithURI;

	public function hook(Hook $hook): void
	{
		/**
		 * Handle legacy option.
		 *
		 * Previously, the option that handle the site visibility was `site_private`
		 * which is now deprecated, replaced with the `site_access` option.
		 *
		 * This filter will ensure that the old value from the `site_private` will
		 * be carried over and mapped to the `site_access` option.
		 */
		$optionCallback = static function ($value) {
			/**
			 * The `site_private` option stores a boolean, which will be stored as
			 * `1` or `""`. If it is `1`, it means that the option is already set.
			 *
			 * If it is already set, respect the value and return it. Otherwise,
			 * fallback to the `site_access` option.
			 *
			 * @see https://developer.wordpress.org/reference/functions/get_option/#description For how WordPress handles option values.
			 */

			if (Option::get('site_private') === '1') {
				return 'private';
			}

			return $value;
		};
		$hook->addFilter(self::defaultOptionHook('site_access'), $optionCallback, PHP_INT_MAX);
		$hook->addFilter(self::optionHook('site_access'), $optionCallback, PHP_INT_MAX);

		/**
		 * Remove the `site_private` option, after the `site_access` option has been
		 * updated, since the `site_private` option is now deprecated.
		 */
		$updateOptionCallback = static function (): void {
			$private = Option::get('site_private');

			if ($private !== '1' && $private !== '') {
				return;
			}

			delete_option(Option::name('site_private'));
		};
		$hook->addAction(self::addOptionHook('site_access'), $updateOptionCallback, PHP_INT_MAX);
		$hook->addAction(self::updateOptionHook('site_access'), $updateOptionCallback, PHP_INT_MAX);

		if (Option::get('site_access') !== 'private') {
			return;
		}

		$hook->addAction('template_redirect', [$this, 'forceLogin'], PHP_INT_MIN);
		$hook->addAction('rightnow_end', [$this, 'showRightNowStatus'], PHP_INT_MIN);
		$hook->addFilter('login_site_html_link', '__return_empty_string', PHP_INT_MAX);
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

	/**
	 * Show the "Private" status at the "At a glance" dashboard widget.
	 */
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
