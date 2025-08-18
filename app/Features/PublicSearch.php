<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use WP_Error;
use WP_REST_Request;
use WP_REST_Server;

use function strpos;

use const PHP_INT_MIN;

final class PublicSearch implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addAction('widgets_init', static fn () => unregister_widget('WP_Widget_Search'));
		$hook->addFilter('rest_pre_dispatch', [self::class, 'disableEndpoint'], PHP_INT_MIN, 3);
		$hook->addFilter('get_search_form', '__return_empty_string');
		$hook->addFilter('pre_get_posts', static function ($query): void {
			if (self::isSearchQuery()) {
				wp_safe_redirect(home_url(), 301);
				exit;
			}
		});
	}

	/**
	 * @param mixed                         $result
	 * @param WP_REST_Request<array<mixed>> $request
	 *
	 * @return mixed
	 */
	public static function disableEndpoint($result, WP_REST_Server $server, WP_REST_Request $request)
	{
		if (is_user_logged_in()) {
			return $result;
		}

		if (strpos($request->get_route(), '/wp/v2/search') !== false) {
			return new WP_Error(
				'rest_no_route',
				'The search endpoint is disabled.',
				['status' => 404],
			);
		}

		return $result;
	}

	private static function isSearchQuery(): bool
	{
		return is_search() && ! is_admin();
	}
}
