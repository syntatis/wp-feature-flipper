<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Codex\Settings\Settings;
use WP_REST_Request;

use function array_filter;
use function array_keys;
use function array_merge;
use function basename;
use function function_exists;
use function in_array;
use function is_array;
use function is_readable;
use function sprintf;

use const ARRAY_FILTER_USE_KEY;
use const PHP_INT_MAX;

class SettingPage implements Hookable
{
	private Settings $settings;

	private string $appName;

	private string $scriptHandle;

	private string $pageName;

	public function __construct(Settings $settings)
	{
		$this->appName = App::name();
		$this->settings = $settings;
		$this->scriptHandle = $this->appName . '-settings';
		$this->pageName = 'settings_page_' . $this->appName;
	}

	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_bar_menu', [$this, 'addInlineScript'], PHP_INT_MAX);
		$hook->addAction('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
		$hook->addAction('admin_menu', [$this, 'addMenu']);
		$hook->addFilter('syntatis/feature_flipper/inline_data', [$this, 'addInlineData']);
		$hook->addFilter(sprintf('plugin_action_links'), [$this, 'addPluginActionLinks'], 10, 2);
	}

	/**
	 * Add the settings menu on WordPress admin.
	 */
	public function addMenu(): void
	{
		add_submenu_page(
			'options-general.php', // Parent slug.
			__('Feature Flipper', 'syntatis-feature-flipper'),
			__('Flipper', 'syntatis-feature-flipper'),
			'manage_options',
			App::name(),
			[$this, 'render'],
		);
	}

	/**
	 * Render the plugin settings page.
	 *
	 * Called when user navigates to the plugin settings page. It will render
	 * only with these HTML. The settings form, inputs, buttons will be
	 * rendered with React components.
	 *
	 * @see ./src/settings/Page.jsx
	 */
	public function render(): void
	{
		// phpcs:disable Generic.Files.InlineHTML.Found
		?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<div id="<?php echo esc_attr(App::name()); ?>-settings"></div>
			<noscript>
				<p>
					<?php esc_html_e('This setting page requires JavaScript to be enabled in your browser. Please enable JavaScript and reload the page.', 'syntatis-feature-flipper'); ?>
				</p>
			</noscript>
		</div>
		<?php
		// phpcs:enable
	}

	/** @param string $adminPage The current admin page. */
	public function enqueueAdminScripts(string $adminPage): void
	{
		if (! $this->isSettingPage()) {
			return;
		}

		$assets = App::dir('dist/assets/setting-page/index.asset.php');
		$assets = is_readable($assets) ? require $assets : [];

		wp_enqueue_style(
			$this->scriptHandle,
			App::url('dist/assets/setting-page/index.css'),
			[$this->appName],
			$assets['version'] ?? null,
		);

		wp_enqueue_script(
			$this->scriptHandle,
			App::url('dist/assets/setting-page/index.js'),
			$assets['dependencies'] ?? [],
			$assets['version'] ?? null,
			true,
		);

		wp_set_script_translations($this->scriptHandle, 'syntatis-feature-flipper');
	}

	public function addInlineScript(): void
	{
		if (! $this->isSettingPage()) {
			return;
		}

		wp_add_inline_script(
			$this->scriptHandle,
			$this->getInlineScript(),
			'before',
		);
	}

	/**
	 * @param array<string,mixed> $data
	 *
	 * @return array<string,mixed>
	 */
	public function addInlineData(array $data): array
	{
		if (! $this->isSettingPage()) {
			return $data;
		}

		return array_merge(
			[
				'settingPage' => get_admin_url(null, 'options-general.php?page=' . App::name()),
				'settingPageTab' => $_GET['tab'] ?? null,
			],
			$data,
		);
	}

	/**
	 * @param array<string> $links
	 *
	 * @return array<string>
	 */
	public function addPluginActionLinks(array $links, string $pluginFile): array
	{
		if (basename($pluginFile, '.php') !== $this->appName) {
			return $links;
		}

		$links = array_merge([
			sprintf(
				'<a href="%1$s">%2$s</a>',
				get_admin_url(null, 'options-general.php?page=' . $this->appName),
				__('Settings', 'syntatis-feature-flipper'),
			),
		], $links);

		return $links;
	}

	/**
	 * Provide the inline script content.
	 */
	private function getInlineScript(): string
	{
		$all = $this->settings->get('all');

		if (! is_array($all) || $all === []) {
			return '';
		}

		$request = new WP_REST_Request('GET', '/wp/v2/settings');
		$response = rest_do_request($request);
		$data = $response->get_data();

		if (! is_array($data) || $data === []) {
			return '';
		}

		/**
		 * Filter the response data to only include those registered in the plugin
		 * settings.
		 */
		$keys = array_keys($all);
		$data = array_filter(
			$data,
			static fn ($key): bool => in_array($key, $keys, true),
			ARRAY_FILTER_USE_KEY,
		);

		return sprintf(
			<<<'SCRIPT'
			wp.apiFetch.use( wp.apiFetch.createPreloadingMiddleware( %s ) )
			SCRIPT,
			wp_json_encode([
				'/wp/v2/settings' => [
					'body' => apply_filters('syntatis/feature_flipper/settings', $data),
				],
			]),
		);
	}

	private function isSettingPage(): bool
	{
		if (! is_admin() || ! function_exists('get_current_screen')) {
			return false;
		}

		$currentScreen = get_current_screen();

		if ($currentScreen === null) {
			return false;
		}

		return $currentScreen->id === $this->pageName;
	}
}
