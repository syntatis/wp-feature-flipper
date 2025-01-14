<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\SiteAccess;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Helpers\Admin;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\Helpers\URL;
use WP_Admin_Bar;

use function defined;
use function printf;
use function sprintf;

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

		$hook->addAction('admin_bar_menu', [$this, 'adminBarMenu'], PHP_INT_MIN);
		$hook->addAction('rightnow_end', [$this, 'showRightNowStatus'], PHP_INT_MIN);
		$hook->addAction('template_redirect', [$this, 'forceLogin'], PHP_INT_MIN);
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

		if (URL::isLogin()) {
			return;
		}

		nocache_headers();
		wp_safe_redirect(wp_login_url(URL::current()), 302);
		exit;
	}

	/**
	 * Show the "Maintenance" status on the admin bar menu.
	 */
	public function adminBarMenu(WP_Admin_Bar $wpAdminBar): void
	{
		if (! is_admin()) {
			return;
		}

		$node = [
			'id' => App::name() . '-site-access',
			'title' => sprintf(
				<<<'HTML'
				<div style="display: flex; align-items: center;">
					<span class="dashicons dashicons-warning ab-icon"></span>
					%s
				</div>
				HTML,
				_x('Private', 'Site access mode', 'syntatis-feature-flipper'),
			),
			'parent' => 'top-secondary',
		];

		if (current_user_can('manage_options') && ! Admin::isScreen(App::name())) {
			$node['href'] = Admin::url(App::name(), ['tab' => 'site']);
		}

		$wpAdminBar->add_node($node);
	}

	/**
	 * Show the "Private" status at the "At a glance" dashboard widget.
	 */
	public function showRightNowStatus(): void
	{
		$message = __('The site is currently in private mode', 'syntatis-feature-flipper');

		if (current_user_can('manage_options')) {
			$message = sprintf(
				'<a href="%s">%s</a>',
				Admin::url(App::name(), ['tab' => 'site']),
				$message,
			);
		}

		printf(
			<<<'HTML'
			<div style="margin-bottom: 5px;">
				<span class="dashicons dashicons-warning"></span>
				%s
			</div>
			HTML,
			wp_kses($message, ['a' => ['href' => true]]),
		);
	}
}
