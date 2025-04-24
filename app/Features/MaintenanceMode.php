<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Action;
use SSFV\Codex\Foundation\Hooks\Filter;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Admin;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Admin_Bar;

use function header;
use function is_array;
use function is_string;
use function printf;
use function sprintf;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

final class MaintenanceMode implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->parse($this);
		$hook->addFilter(Option::hook('sanitize:site_maintenance_args'), [$this, 'sanitizeArgsOption'], PHP_INT_MAX);
		$hook->addFilter(Option::hook('default:site_maintenance_args'), static function (): array {
			return [
				'headline' => __(
					'Under Maintenance 🚧',
					'syntatis-feature-flipper',
				),
				'message' => __(
					'We are currently performing some scheduled maintenance. We will be back as soon as possible.',
					'syntatis-feature-flipper',
				),
			];
		}, PHP_INT_MAX);
	}

	#[Action(name: 'template_redirect', priority: PHP_INT_MIN)]
	public function templateRedirect(): void
	{
		if (! self::isMaintenanceMode() || is_user_logged_in()) {
			return;
		}

		status_header(503);
		header('Retry-After: ' . 3600);

		$args = Option::get('site_maintenance_args');
		$args = is_array($args) ? $args : [];
		$args = [
			'headline' => $args['headline'] ?? '',
			'message' => $args['message'] ?? '',
		];

		include App::dir('inc/views/maintenance-mode.php');

		exit;
	}

	#[Filter(name: 'wp_title', priority: PHP_INT_MAX, acceptedArgs: 2)]
	public function pageTitle(string $title, string $separator): string
	{
		if (! self::isMaintenanceMode()) {
			return $title;
		}

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
	#[Action(name: 'admin_bar_menu', priority: PHP_INT_MIN)]
	public function adminBarMenu(WP_Admin_Bar $wpAdminBar): void
	{
		if (! self::isMaintenanceMode()) {
			return;
		}

		// Only show the node if the user is logged in and has access to the admin bar.
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

		if (current_user_can('manage_options') && ! Admin::isScreen(App::name())) {
			$node['href'] = Admin::url(App::name(), ['tab' => 'site']);
		}

		$wpAdminBar->add_node($node);
	}

	/**
	 * Show the "Private" status at the "At a glance" dashboard widget.
	 */
	#[Action(name: 'rightnow_end', priority: PHP_INT_MIN)]
	public function showRightNowStatus(): void
	{
		if (! self::isMaintenanceMode()) {
			return;
		}

		$message = __('The site is currently in Maintenance mode', 'syntatis-feature-flipper');

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
				<span class="dashicons dashicons-info"></span>
				%s
			</div>
			HTML,
			wp_kses($message, ['a' => ['href' => true]]),
		);
	}

	/**
	 * @param mixed $value The value of the "site_maintenance_args" option.
	 *
	 * @return array<string,string>
	 */
	public function sanitizeArgsOption(mixed $value): array
	{
		$value = is_array($value) ? $value : [];
		$headline = $value['headline'] ?? '';
		$message = $value['message'] ?? '';

		return [
			'headline' => wp_strip_all_tags(is_string($headline) ? $headline : ''),
			'message' => wp_strip_all_tags(is_string($message) ? $message : ''),
		];
	}

	private static function isMaintenanceMode(): bool
	{
		return Option::get('site_access') === 'maintenance';
	}
}
