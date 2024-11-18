<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Facades\Config;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Option;
use WP;
use WP_Scripts;

use function array_diff;
use function defined;
use function is_readable;
use function is_string;
use function strpos;

use const PHP_INT_MAX;

class Embeds implements Hookable
{
	public function hook(Hook $hook): void
	{
		if ((bool) Option::get('embed')) {
			return;
		}

		$hook->addAction('init', fn () => $this->disables($hook), PHP_INT_MAX);
	}

	private function disables(Hook $hook): void
	{
		// phpcs:disable
		/** @var WP $wp */
		$wp = $GLOBALS['wp'];
		$wp->public_query_vars = array_diff($wp->public_query_vars, ['embed']);
		// phpcs:enable

		$hook->addAction('update_option_' . Config::get('app.option_prefix') . 'cron', [$this, 'flushPermalinks']);
		$hook->addAction('enqueue_block_editor_assets', [$this, 'disableOnBlockEditor']);
		$hook->addAction('wp_default_scripts', [$this, 'disableScriptDependencies']);
		$hook->addFilter('embed_oembed_discover', '__return_false');
		$hook->addFilter('oembed_response_data', [$this, 'disableResponseData']);
		$hook->addFilter('rest_endpoints', [$this, 'disableEndpoint']);
		$hook->addFilter('rewrite_rules_array', [$this, 'disableRewrite']);
		$hook->addFilter('tiny_mce_plugins', [$this, 'disableOnTinyEditor']);

		$hook->removeAction('wp_head', 'wp_oembed_add_discovery_links');
		$hook->removeAction('wp_head', 'wp_oembed_add_host_js');
		$hook->removeFilter('oembed_dataparse', 'wp_filter_oembed_result', 10);
		$hook->removeFilter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
	}

	public function flushPermalinks(): void
	{
		flush_rewrite_rules(false);
	}

	/**
	 * @param array<mixed> $data
	 *
	 * @return array<mixed>|false
	 */
	public function disableResponseData(array $data)
	{
		if (defined('REST_REQUEST') && REST_REQUEST) {
			return false;
		}

		return $data;
	}

	/**
	 * @param array<mixed> $endpoints
	 *
	 * @return array<mixed>
	 */
	public function disableEndpoint(array $endpoints): array
	{
		if (isset($endpoints['/oembed/1.0/embed'])) {
			unset($endpoints['/oembed/1.0/embed']);
		}

		return $endpoints;
	}

	/**
	 * @param array<mixed> $rules
	 *
	 * @return array<mixed>
	 */
	public function disableRewrite(array $rules): array
	{
		foreach ($rules as $rule => $rewrite) {
			if (is_string($rewrite) && strpos($rewrite, 'embed=true') === false) {
				continue;
			}

			unset($rules[$rule]);
		}

		return $rules;
	}

	/**
	 * @param array<string> $plugins
	 *
	 * @return array<string>
	 */
	public function disableOnTinyEditor(array $plugins): array
	{
		return array_diff($plugins, ['wpembed']);
	}

	public function disableOnBlockEditor(): void
	{
		$assetFile = App::dir('dist/assets/embeds/index.asset.php');

		/** @phpstan-var array{dependencies?:array<string>,version?:string} $asset */
		$asset = is_readable($assetFile) ? require $assetFile : [];
		$asset['dependencies'] ??= [];
		$asset['version'] ??= null;

		wp_enqueue_script(
			App::name() . '-embeds',
			App::url('dist/assets/embeds/index.js'),
			$asset['dependencies'],
			$asset['version'],
			true,
		);
	}

	public function disableScriptDependencies(WP_Scripts $scripts): void
	{
		if (! isset($scripts->registered['wp-edit-post'])) {
			return;
		}

		$scripts->registered['wp-edit-post']->deps = array_diff(
			$scripts->registered['wp-edit-post']->deps,
			['wp-embed'],
		);
	}
}
