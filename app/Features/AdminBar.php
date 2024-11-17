<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;
use WP_Admin_Bar;

use function in_array;
use function json_encode;
use function sprintf;

use const PHP_INT_MAX;

class AdminBar implements Hookable
{
	private const EXCLUDED_MENU = [
		'menu-toggle',
		'site-name',
		'top-secondary',
	];

	/** @var array<array{id:string}> */
	private static array $menu = [];

	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_bar_menu', static fn () => self::$menu = self::getRegisteredMenu(), PHP_INT_MAX);
		$hook->addAction('admin_bar_menu', [$this, 'addInlineScripts'], PHP_INT_MAX);
		$hook->addAction('admin_bar_menu', static function ($wpAdminBar): void {
			$adminBarMenu = Option::get('admin_bar_menu');

			if ($adminBarMenu === null) {
				return;
			}

			foreach (self::$menu as $menu) {
				if ($menu['id'] && in_array($menu['id'], $adminBarMenu, true)) {
					continue;
				}

				$wpAdminBar->remove_node($menu['id']);
			}
		}, PHP_INT_MAX);

		if (! Option::get('admin_bar_howdy')) {
			$hook->addFilter('admin_bar_menu', function ($wpAdminBar) use ($hook): void {
				$hook->removeAction('admin_bar_menu', 'wp_admin_bar_my_account_item', PHP_INT_MAX);
				$this->addMyAccountMenu($wpAdminBar);
			}, PHP_INT_MAX);
		}

		$hook->addFilter('show_admin_bar', static fn () => (bool) Option::get('admin_bar'));
	}

	public function addInlineScripts(): void
	{
		wp_add_inline_script(
			App::name()	. '-settings',
			self::getInlineScript(),
			'before',
		);
	}

	private static function getInlineScript(): string
	{
		return sprintf(
			<<<'SCRIPT'
			window.$syntatis.featureFlipper = Object.assign({}, window.$syntatis.featureFlipper, {
				adminBarMenu: %s,
			});
			SCRIPT,
			json_encode(self::$menu),
		);
	}

	/** @return array<array{id:string}> */
	private static function getRegisteredMenu(): array
	{
		/** @var WP_Admin_Bar $wpAdminBarMenu */
		$wpAdminBarMenu = $GLOBALS['wp_admin_bar'];
		$nodes = $wpAdminBarMenu->get_nodes();
		$items = [];

		foreach ($nodes as $node) {
			$nodeParent = $node->parent || false;

			if (! $nodeParent === false || in_array($node->id, self::EXCLUDED_MENU, true)) {
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
		$class = ( $avatar ? 'with-avatar' : 'no-avatar' );

		$wpAdminBar->add_menu([
			'id'     => 'my-account',
			'parent' => 'top-secondary',
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress convention.
			'title'  => $currentUser->display_name . $avatar,
			'href'   => get_edit_profile_url($userId),
			'meta'   => ['class' => $class],
		]);
	}
}
