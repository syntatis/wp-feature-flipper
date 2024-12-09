<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Admin_Bar;

use function count;
use function in_array;
use function is_array;
use function is_string;

use const PHP_INT_MAX;

class AdminBar implements Hookable
{
	private const EXCLUDED_MENU = [
		'menu-toggle',
		'site-name',
		'top-secondary',
	];

	public function hook(Hook $hook): void
	{
		$hook->addFilter('syntatis/feature_flipper/inline_data', [$this, 'setInlineData']);
		$hook->addAction('admin_bar_menu', static function ($wpAdminBar): void {
			$adminBarMenu = Option::get('admin_bar_menu');

			if (! is_array($adminBarMenu)) {
				return;
			}

			$menu = self::getRegisteredMenu();

			foreach ($menu as $item) {
				if (in_array($item['id'], $adminBarMenu, true)) {
					continue;
				}

				$wpAdminBar->remove_node($item['id']);
			}
		}, PHP_INT_MAX);

		if (! (bool) Option::get('admin_bar_howdy')) {
			$hook->addFilter('admin_bar_menu', function ($wpAdminBar) use ($hook): void {
				$hook->removeAction('admin_bar_menu', 'wp_admin_bar_my_account_item', PHP_INT_MAX);
				$this->addMyAccountMenu($wpAdminBar);
			}, PHP_INT_MAX);
		}

		$hook->addFilter('show_admin_bar', static fn () => (bool) Option::get('admin_bar'));
	}

	/**
	 * @param array<string,mixed> $data
	 *
	 * @return array<string,mixed>
	 */
	public function setInlineData(array $data): array
	{
		$data['adminBarMenu'] = self::getRegisteredMenu();

		return $data;
	}

	/** @return array<array{id:string}> */
	private static function getRegisteredMenu(): array
	{
		/** @var array<array{id:string}>|null $items */
		static $items = null;

		if (is_array($items) && count($items) > 0) {
			return $items;
		}

		/** @var WP_Admin_Bar $wpAdminBarMenu */
		$wpAdminBarMenu = $GLOBALS['wp_admin_bar'];
		$nodes = $wpAdminBarMenu->get_nodes();
		$items = [];

		if (! is_array($nodes)) {
			return $items;
		}

		foreach ($nodes as $node) {
			$nodeParent = $node->parent;

			if ($nodeParent !== false || in_array($node->id, self::EXCLUDED_MENU, true)) {
				continue;
			}

			$items[] = [
				'id' => $node->id,
			];
		}

		return $items;
	}

	private function addMyAccountMenu(WP_Admin_Bar $wpAdminBar): void
	{
		$currentUser = wp_get_current_user();
		$userId = get_current_user_id();
		$avatar = get_avatar($userId, 26);

		$wpAdminBar->add_menu([
			'id' => 'my-account',
			'parent' => 'top-secondary',
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress convention.
			'title' => $currentUser->display_name . $avatar,
			'href' => get_edit_profile_url($userId),
			'meta' => ['class' => is_string($avatar) && $avatar !== '' ? 'with-avatar' : 'no-avatar'],
		]);
	}
}
