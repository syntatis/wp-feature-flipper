<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Codex\Settings\Settings;
use Syntatis\FeatureFlipper\Helpers\Admin;
use Syntatis\FeatureFlipper\Helpers\Assets;
use WP_REST_Request;
use WP_Screen;

use function array_filter;
use function array_keys;
use function array_merge;
use function base64_decode;
use function basename;
use function explode;
use function in_array;
use function is_array;
use function is_string;
use function sprintf;
use function strip_tags;
use function trim;

use const ARRAY_FILTER_USE_KEY;
use const PHP_INT_MAX;

final class SettingPage implements Hookable
{
	private Settings $settings;

	/** @phpstan-var non-empty-string */
	private string $appName;

	private string $scriptHandle;

	private string $inlineData = '';

	public function __construct(Settings $settings)
	{
		$this->settings = $settings;
		$this->appName = App::name();
		$this->scriptHandle = $this->appName . '-settings';
	}

	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_bar_menu', [$this, 'addInlineScript'], PHP_INT_MAX);
		$hook->addAction('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
		$hook->addAction('admin_menu', [$this, 'addMenu']);
		$hook->addAction('load-settings_page_' . $this->appName, [$this, 'addHelpTab']);
		$hook->addFilter('plugin_action_links', [$this, 'filterPluginActionLinks'], 10, 2);
	}

	public function addMenu(): void
	{
		add_submenu_page(
			'options-general.php', // Parent slug.
			__('Feature Settings', 'syntatis-feature-flipper'),
			__('Feature', 'syntatis-feature-flipper'),
			'manage_options',
			$this->appName,
			function (): void {
				$args = [
					'inline_data' => $this->inlineData,
				];

				include App::dir('inc/views/settings-page.php');
			},
		);
	}

	/** @param string $adminPage The current admin page. */
	public function enqueueAdminScripts(string $adminPage): void
	{
		if (! Admin::isScreen($this->appName)) {
			return;
		}

		$manifest = Assets::manifest('dist/assets/setting-page/index.asset.php');
		$this->initInlineData();

		wp_enqueue_style(
			$this->scriptHandle,
			App::url('dist/assets/setting-page/index.css'),
			[$this->appName . '-common'],
			$manifest['version'],
		);

		wp_enqueue_script(
			$this->scriptHandle,
			App::url('dist/assets/setting-page/index.js'),
			$manifest['dependencies'],
			$manifest['version'],
			true,
		);

		wp_set_script_translations($this->scriptHandle, 'syntatis-feature-flipper');
	}

	public function addInlineScript(): void
	{
		if (! Admin::isScreen($this->appName)) {
			return;
		}

		wp_add_inline_script(
			$this->scriptHandle,
			$this->getInlineScript(),
			'before',
		);
	}

	/**
	 * @param array<string> $links
	 *
	 * @return array<string>
	 */
	public function filterPluginActionLinks(array $links, string $pluginFile): array
	{
		if (basename($pluginFile, '.php') !== $this->appName) {
			return $links;
		}

		$links = array_merge([
			sprintf(
				'<a href="%1$s">%2$s</a>',
				Admin::url($this->appName),
				__('Settings', 'syntatis-feature-flipper'),
			),
		], $links);

		return $links;
	}

	private function initInlineData(): void
	{
		/**
		 * @see Syntatis\FeatureFlipper\SettingPage::render() Where the nonce is created.
		 * @see src/setting-page/form/useSettings.js Where the "nonce" & the "options" query are set in the URL.
		 */
		$nonce = $_GET['nonce'] ?? '';
		$options = $_GET['options'] ?? '';

		if (
			is_string($options) &&
			is_string($nonce) &&
			wp_verify_nonce($nonce, $this->appName . '-settings') !== false
		) {
			$options = base64_decode(strip_tags(trim($options)), true);

			/** @internal For internal use. Subject to change. External plugin should not rely on this hook. */
			do_action('syntatis/feature_flipper/updated_options', explode(',', (string) $options));
		}

		$this->inlineData = (string) wp_json_encode(new InlineData());
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
				'/wp/v2/settings' => ['body' => $data],
			]),
		);
	}

	public function addHelpTab(): void
	{
		$screen = get_current_screen();

		if ($screen instanceof WP_Screen === false) {
			return;
		}

		$screen->add_help_tab([
			'id' => $this->appName . '-about',
			'title' => __('About', 'syntatis-feature-flipper'),
			'content' => sprintf(
				<<<'HTML'
				<p>%s</p>
				<p>%s</p>
				<p>%s</p>
				HTML,
				sprintf(
					// translators: %s the link to the plugin page in WordPress.org.
					__('This setting page is provided by the %s plugin.', 'syntatis-feature-flipper'),
					'<strong><a href="https://wordpress.org/plugins/syntatis-feature-flipper/" target="blank" rel="noopener">Feature Flipper</a></strong>',
				),
				__('Here, you can manage some of the core WordPress features like Comments, the Block Editor (Gutenberg), Emojis, XML-RPC, Feeds, Updates, Automatic Updates, Cron, Heartbeat, and more. If you don\'t need certain features, you can easily toggle them off or customize their behavior.', 'syntatis-feature-flipper'),
				__('The plugin also includes additional utility features, which you can enable or disable as needed.', 'syntatis-feature-flipper'),
			),
		]);
		$screen->set_help_sidebar(
			sprintf(
				<<<'HTML'
				<p><strong>%s</strong></p>
				<ul>
					<li>%s</li>
					<li>%s</li>
				</ul>
				HTML,
				__('For more information:', 'syntatis-feature-flipper'),
				'<a href="https://wordpress.org/support/plugin/syntatis-feature-flipper/" target="blank" rel="noopener">' . __('Support forums', 'syntatis-feature-flipper') . '</a>',
				'<a href="https://github.com/syntatis/wp-feature-flipper" target="blank" rel="noopener">' . __('Github.com Repository', 'syntatis-feature-flipper') . '</a>',
			),
		);
	}
}
