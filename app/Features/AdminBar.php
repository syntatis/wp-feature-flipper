<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\AdminBar\RegisteredMenu;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Admin_Bar;

use function array_keys;
use function array_merge;
use function in_array;
use function is_array;
use function is_readable;
use function is_string;
use function json_decode;
use function json_encode;
use function sort;
use function sprintf;

use const PHP_INT_MAX;

class AdminBar implements Hookable
{
	private string $appName;

	public function __construct()
	{
		$this->appName = App::name();
	}

	public function hook(Hook $hook): void
	{
		$hook->addAction('syntatis/feature_flipper/updated_options', [$this, 'stashOptions']);
		$hook->addFilter('syntatis/feature_flipper/inline_data', [$this, 'filterInlineData']);
		$hook->addFilter(
			Option::hook('default:admin_bar_menu'),
			static fn () => self::getRegisteredMenu(),
			PHP_INT_MAX,
		);
		$hook->addFilter(
			Option::hook('admin_bar_menu'),
			static fn ($value) => Option::patch(
				'admin_bar_menu',
				is_array($value) ? $value : [],
				self::getRegisteredMenu(),
			),
			PHP_INT_MAX,
		);
		$hook->addAction('admin_bar_menu', [$this, 'removeNodes'], PHP_INT_MAX);
		$hook->addAction('admin_enqueue_scripts', [$this, 'enqueueScripts']);
		$hook->addAction('wp_enqueue_scripts', [$this, 'enqueueScripts']);
		$hook->addFilter('show_admin_bar', [$this, 'showAdminBar'], PHP_INT_MAX);

		if (! Option::isOn('admin_bar_howdy')) {
			$hook->addAction('admin_bar_menu', [$this, 'addMyAccountNode'], PHP_INT_MAX);
		}

		if (! Option::isOn('admin_bar_env_type')) {
			return;
		}

		$hook->addAction('admin_bar_menu', [$this, 'addEnvironmentTypeNode']);
	}

	/** @param array<string> $options List of option names that have been updated. */
	public function stashOptions(array $options): void
	{
		if (! in_array(Option::name('admin_bar_menu'), $options, true)) {
			return;
		}

		Option::stash('admin_bar_menu', self::getRegisteredMenu());
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

		$menu = self::getRegisteredMenu();
		$curr = $data['$wp'] ?? [];
		$data['$wp'] = array_merge(
			is_array($curr) ? $curr : [],
			['adminBarMenu' => $menu],
		);

		return $data;
	}

	public function enqueueScripts(): void
	{
		if (! is_user_logged_in()) {
			return;
		}

		if (! (bool) Option::get('admin_bar_env_type')) {
			return;
		}

		$assets = App::dir('dist/assets/admin-bar/index.asset.php');
		$assets = is_readable($assets) ? require $assets : [];

		wp_enqueue_style(
			$this->appName . '-admin-bar',
			App::url('dist/assets/admin-bar/index.css'),
			[$this->appName . '-common'],
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

	public function removeNodes(WP_Admin_Bar $wpAdminBar): void
	{
		$adminBarMenu = Option::get('admin_bar_menu');

		if (! is_array($adminBarMenu)) {
			return;
		}

		foreach (self::getRegisteredMenu() as $menuId) {
			if (in_array($menuId, $adminBarMenu, true)) {
				continue;
			}

			$wpAdminBar->remove_node($menuId);
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
		$id = $this->appName . '-environment-type';
		$inlineData = wp_json_encode(['environmentType' => wp_get_environment_type()]);
		$wpAdminBar->add_node(
			[
				'id' => $id,
				'title' => sprintf(
					<<<HTML
					<div id="%s" data-inline='$inlineData'></div>
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
		return is_user_logged_in() ? Option::isOn('admin_bar') : $value;
	}

	/** @return array<string> */
	private static function getRegisteredMenu(): array
	{
		$menu = array_keys(RegisteredMenu::all('top'));

		sort($menu);

		return $menu;
	}
}
