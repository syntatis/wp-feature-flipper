<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Codex\Settings\Settings;
use Syntatis\FeatureFlipper\Concerns\WithAdmin;
use Syntatis\FeatureFlipper\Concerns\WithPostTypes;
use WP_REST_Request;

use function array_filter;
use function array_keys;
use function array_merge;
use function base64_decode;
use function basename;
use function explode;
use function in_array;
use function is_array;
use function is_readable;
use function is_string;
use function sprintf;
use function strip_tags;
use function trim;

use const ARRAY_FILTER_USE_KEY;
use const PHP_INT_MAX;

class SettingPage implements Hookable
{
	use WithAdmin;
	use WithPostTypes;

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
		$hook->addAction('admin_bar_menu', [$this, 'initInlineData'], PHP_INT_MAX - 1); // Important: Make sure this runs before the inline script.
		$hook->addAction('admin_bar_menu', [$this, 'addInlineScript'], PHP_INT_MAX);
		$hook->addAction('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
		$hook->addAction('admin_menu', [$this, 'addMenu']);
		$hook->addFilter('plugin_action_links', [$this, 'filterPluginActionLinks'], 10, 2);
	}

	public function addMenu(): void
	{
		add_submenu_page(
			'options-general.php', // Parent slug.
			__('Feature Flipper', 'syntatis-feature-flipper'),
			__('Flipper', 'syntatis-feature-flipper'),
			'manage_options',
			$this->appName,
			[$this, 'render'],
		);
	}

	public function initInlineData(): void
	{
		if (! Admin::isScreen($this->appName)) {
			return;
		}

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
			$options = explode(',', (string) $options);

			/** For internal use. Subject to change. External plugin should not rely on hook. */
			do_action('syntatis/feature_flipper/updated_options', $options);
		}

		$this->inlineData = (string) wp_json_encode(
			/** For internal use. Subject to change. External plugin should not rely on this hook. */
			apply_filters('syntatis/feature_flipper/inline_data', [
				'$wp' => [
					'postTypes' => self::getRegisteredPostTypes(),
					'themeSupport' => [
						'widgetsBlockEditor' => get_theme_support('widgets-block-editor'),
					],
				],
				'settingPage' => esc_url(get_admin_url(null, 'options-general.php?page=' . $this->appName)) ,
				'settingPageTab' => sanitize_key(isset($_GET['tab']) && is_string($_GET['tab']) ? $_GET['tab'] : ''),
			]),
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
			<div
				id="<?php echo esc_attr($this->appName); ?>-settings"
				data-nonce="<?php echo esc_attr(wp_create_nonce($this->appName . '-settings')); ?>"
				data-inline='<?php echo esc_attr($this->inlineData); ?>'>
			</div>
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
		if (! Admin::isScreen($this->appName)) {
			return;
		}

		$assets = App::dir('dist/assets/setting-page/index.asset.php');
		$assets = is_readable($assets) ? require $assets : [];

		wp_enqueue_style(
			$this->scriptHandle,
			App::url('dist/assets/setting-page/index.css'),
			[$this->appName . '-common'],
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
}
