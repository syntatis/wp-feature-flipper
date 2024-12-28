<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Admin_Bar;

use function array_merge;
use function count;
use function in_array;
use function is_array;
use function is_readable;
use function is_string;
use function json_decode;
use function json_encode;
use function md5;
use function sprintf;

use const PHP_INT_MAX;

class AdminBar implements Hookable
{
	/**
	 * List of menu in the admin bar that should be excluded.
	 *
	 * - menu-toggle: The menu toggle button shown before the site name on small screen.
	 * - site-name: Shows the site name that links to visit the site homepage.
	 * - top-secondary: The top secondary menu which contains the user profile and the logout link.
	 */
	private const DEFAULT_EXCLUDED_MENU = [
		'menu-toggle',
		'site-name',
		'top-secondary',
	];

	private string $appName;

	public function __construct()
	{
		$this->appName = App::name();
	}

	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_bar_menu', [$this, 'removeNodes'], PHP_INT_MAX);
		$hook->addAction('admin_enqueue_scripts', [$this, 'enqueueScripts']);
		$hook->addAction('wp_enqueue_scripts', [$this, 'enqueueScripts']);

		if (! (bool) Option::get('admin_bar_howdy')) {
			$hook->addAction('admin_bar_menu', [$this, 'addMyAccountNode'], PHP_INT_MAX);
		}

		if ((bool) Option::get('admin_bar_env_type')) {
			$hook->addAction('admin_bar_menu', [$this, 'addEnvironmentTypeNode']);
		}

		$hook->addFilter('show_admin_bar', [$this, 'showAdminBar'], PHP_INT_MAX);
		$hook->addFilter('syntatis/feature_flipper/inline_data', [$this, 'filterInlineData'], PHP_INT_MAX);
	}

	/**
	 * Provide additional data to include in the plugin's global inline data.
	 *
	 * @param array<string,mixed> $data
	 *
	 * @return array<string,mixed>
	 */
	public function filterInlineData(array $data): array
	{
		$tab = $_GET['tab'] ?? null;

		if ($tab !== 'admin') {
			return $data;
		}

		$curr = $data['$wp'] ?? [];
		$data['$wp'] = array_merge(
			is_array($curr) ? $curr : [],
			['adminBarMenu' => self::getRegisteredMenu()],
		);

		return $data;
	}

	public function enqueueScripts(): void
	{
		$assets = App::dir('dist/assets/admin-bar/index.asset.php');
		$assets = is_readable($assets) ? require $assets : [];

		wp_enqueue_style(
			$this->appName . '-admin-bar',
			App::url('dist/assets/admin-bar/index.css'),
			[$this->appName],
			$assets['version'] ?? null,
		);

		wp_enqueue_script(
			$this->appName . '-admin-bar',
			App::url('dist/assets/admin-bar/index.js'),
			$assets['dependencies'] ?? [],
			$assets['version'] ?? null,
			false,
		);
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

			if ($nodeParent !== false || in_array($node->id, self::getExcludedMenu(), true)) {
				continue;
			}

			$items[] = [
				'id' => $node->id,
			];
		}

		return $items;
	}

	public function removeNodes(WP_Admin_Bar $wpAdminBar): void
	{
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
	}

	public function addMyAccountNode(WP_Admin_Bar $wpAdminBar): void
	{
		$currentUser = wp_get_current_user();
		$userId = get_current_user_id();
		$avatar = get_avatar($userId, 26);

		$wpAdminBar->remove_node('my-account');
		$wpAdminBar->add_menu([
			'id' => 'my-account',
			'parent' => 'top-secondary',
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress convention.
			'title' => $currentUser->display_name . $avatar,
			'href' => get_edit_profile_url($userId),
			'meta' => ['class' => is_string($avatar) && $avatar !== '' ? 'with-avatar' : 'no-avatar'],
		]);
	}

	public function addEnvironmentTypeNode(WP_Admin_Bar $wpAdminBar): void
	{
		$id = md5($this->appName . '-environment-type');
		$inlineData = wp_json_encode(['environmentType' => wp_get_environment_type()]);
		$wpAdminBar->add_node(
			[
				'id' => $id,
				'title' => sprintf(
					<<<HTML
					<div id="%s-root" data-inline='$inlineData'></div>
					HTML,
					$id,
				),
				'parent' => 'top-secondary',
			],
		);

		$myAccNode = json_encode($wpAdminBar->get_node('my-account'));

		if (! is_string($myAccNode)) {
			return;
		}

		$wpAdminBar->remove_node('my-account');

		/** @var array{id?:string,title?:string,parent?:string,meta?:array<string,mixed>}|false $myAccNodeDecoded */
		$myAccNodeDecoded = json_decode($myAccNode, true);

		if (! is_array($myAccNodeDecoded)) {
			return;
		}

		$wpAdminBar->add_node($myAccNodeDecoded);
	}

	public function showAdminBar(bool $value): bool
	{
		return is_user_logged_in() ? (bool) Option::get('admin_bar') : $value;
	}

	/** @return array<string> */
	private static function getExcludedMenu(): array
	{
		$excludes = self::DEFAULT_EXCLUDED_MENU;

		if (! (bool) Option::get('comments')) {
			$excludes = [
				...$excludes,
				'comments',
			];
		}

		return $excludes;
	}
}
