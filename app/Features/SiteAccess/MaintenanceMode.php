<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\SiteAccess;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithAdmin;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Admin_Bar;

use function header;
use function printf;
use function sprintf;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

class MaintenanceMode implements Hookable
{
	use WithAdmin;

	public function hook(Hook $hook): void
	{
		if (Option::get('site_access') !== 'maintenance') {
			return;
		}

		$hook->addAction('admin_bar_menu', [$this, 'adminBarMenu'], PHP_INT_MIN);
		$hook->addAction('rightnow_end', [$this, 'showRightNowStatus'], PHP_INT_MIN);
		$hook->addAction('template_redirect', [$this, 'templateRedirect'], PHP_INT_MIN);
		$hook->addFilter('wp_title', [$this, 'filterPageTitle'], PHP_INT_MAX, 2);
	}

	public function templateRedirect(): void
	{
		if (is_user_logged_in()) {
			return;
		}

		status_header(503);

		header('Retry-After: ' . 3600);

		include App::dir('inc/views/maintenance-mode.php');

		exit;
	}

	public function filterPageTitle(string $title, string $separator): string
	{
		return sprintf(
			'%s %s %s',
			_x('Under Maintenance', 'Maintenance page title', 'syntatis-feature-flipper'),
			$separator,
			get_bloginfo('name'),
		);
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
					<span class="dashicons dashicons-info ab-icon"></span>%s
				</div>
				HTML,
				_x('Maintenance', 'Site access mode', 'syntatis-feature-flipper'),
			),
			'parent' => 'top-secondary',
		];

		if (current_user_can('manage_options') && ! self::isSettingPage()) {
			$node['href'] = self::getSettingPageURL(['tab' => 'site']);
		}

		$wpAdminBar->add_node($node);
	}

	/**
	 * Show the "Private" status at the "At a glance" dashboard widget.
	 */
	public function showRightNowStatus(): void
	{
		$message = __('The site is currently in maintenance mode', 'syntatis-feature-flipper');

		if (current_user_can('manage_options')) {
			$message = sprintf(
				'<a href="%s">%s</a>',
				self::getSettingPageURL(['tab' => 'site']),
				$message,
			);
		}

		printf(
			<<<'HTML'
			<div style="margin-bottom: 5px;">
				<span class="dashicons dashicons-info"></span>
				%s
			</div>
			HTML,
			wp_kses($message, ['a' => ['href' => true]]),
		);
	}
}
