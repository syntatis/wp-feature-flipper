<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features\AdminBar;

use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Admin_Bar;

use function array_merge;
use function in_array;
use function is_array;

/** @phpstan-type RegisteredMenuType = array{id:string,parent?:string|false} */
final class RegisteredMenu
{
	/**
	 * List of menu in the admin bar that should be excluded.
	 *
	 * - menu-toggle: The menu toggle button shown before the site name on small screen.
	 * - site-name: Shows the site name that links to visit the site homepage.
	 * - my-account: The user account menu, which includes the user's display name and avatar.
	 */
	private const EXCLUDE_MENU_ITEMS = [
		'menu-toggle',
		'site-name',
		'my-account',
		'top-secondary',
	];

	/**
	 * List of Core menu that may not be identifiable in the Admin screen.
	 */
	private const INCLUDE_CORE_MENU_ITEMS = [
		'customize' => [
			'id' => 'customize',
			'parent' => false,
		],
		'edit' => [
			'id' => 'edit',
			'parent' => false,
		],
		'search' => [
			'id' => 'search',
			'parent' => false,
		],
	];

	private ?WP_Admin_Bar $wpAdminBar = null;

	/** @phpstan-var array<non-empty-string,RegisteredMenuType> */
	private array $registeredMenu;

	private function __construct()
	{
		/** @var WP_Admin_Bar $wpAdminBar */
		$wpAdminBar = $GLOBALS['wp_admin_bar'] ?? null;

		$this->wpAdminBar = $wpAdminBar;
		$this->registeredMenu = $this->getRegisteredMenu();
	}

	/**
	 * @phpstan-param "top"|null $level The level of the menu to retrieve.
	 *
	 * @phpstan-return array<non-empty-string,RegisteredMenuType>
	 */
	public static function all(?string $level = null): array
	{
		$instance = new self();

		switch ($level) {
			case 'top':
				return $instance->getTopItems();

			default:
				return $instance->registeredMenu;
		}
	}

	/**
	 * Retrieve top-level menu of the admin bar.
	 *
	 * @phpstan-return array<non-empty-string,RegisteredMenuType>
	 */
	private function getTopItems(): array
	{
		$topItems = [];

		foreach ($this->registeredMenu as $key => $menu) {
			$menuParent = $menu['parent'] ?? null;

			/**
			 * If the node is not a top-level node or a node within the `top-secondary`,
			 * skip it. The `top-secondary` section may contain some menus items such
			 * as the "environment type" and "site status" menu. Even though these
			 * menu are technically not top-level menus, they are considered as
			 * such since users can see them on the top-level and might want
			 * to toggle them on or off.
			 *
			 * @see Syntatis\FeatureFlipper\Features\AdminBar::addEnvironmentTypeNode()
			 * @see Syntatis\FeatureFlipper\Features\SiteAccess\MaintenanceMode
			 * @see Syntatis\FeatureFlipper\Features\SiteAccess\PrivateMode
			 */
			if ($menuParent !== false && $menuParent !== 'top-secondary') {
				continue;
			}

			if (in_array($key, self::getExcludedMenu(), true)) {
				continue;
			}

			$topItems[$key] = $menu;
		}

		return $topItems;
	}

	/** @phpstan-return array<non-empty-string,RegisteredMenuType> */
	private function getRegisteredMenu(): array
	{
		$nodes = $this->wpAdminBar instanceof WP_Admin_Bar ? $this->wpAdminBar->get_nodes() : [];
		$items = [];

		if (! is_array($nodes)) {
			return $items;
		}

		foreach ($nodes as $id => $node) {
			$items[$node->id] = [
				'id' => $node->id,
				'parent' => $node->parent,
			];
		}

		return array_merge($items, self::INCLUDE_CORE_MENU_ITEMS);
	}

	/** @return array<string> */
	private static function getExcludedMenu(): array
	{
		$excludes = self::EXCLUDE_MENU_ITEMS;

		if (! Option::isOn('comments')) {
			$excludes = [
				...$excludes,
				'comments',
			];
		}

		return $excludes;
	}
}
