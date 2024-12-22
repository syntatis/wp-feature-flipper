<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use WP_Post_Type;

use function array_filter;
use function array_map;
use function in_array;
use function sprintf;

use const ARRAY_FILTER_USE_BOTH;
use const PHP_INT_MAX;

class CommonScripts implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_bar_menu', [$this, 'addInlineScript'], PHP_INT_MAX);
		$hook->addAction('wp_enqueue_scripts', [$this, 'enqueueScripts'], PHP_INT_MAX);
		$hook->addAction('admin_enqueue_scripts', [$this, 'enqueueScripts'], PHP_INT_MAX);
	}

	public function enqueueScripts(): void
	{
		if (! is_user_logged_in()) {
			return;
		}

		wp_enqueue_style(App::name(), App::url('dist/assets/index.css'));
	}

	public function addInlineScript(): void
	{
		wp_print_inline_script_tag($this->getInlineScript());
	}

	private function getInlineScript(): string
	{
		return sprintf(
			<<<'SCRIPT'
			window.$syntatis = %s;
			SCRIPT,
			wp_json_encode(
				apply_filters('syntatis/inline_data', [
					'environmentType' => wp_get_environment_type(),
					'postTypes' => $this->getPostTypes(),
					'themeSupport' => [
						'widgetsBlockEditor' => get_theme_support('widgets-block-editor'),
					],
				]),
			),
		);
	}

	/** @return array<string,array<string,mixed>> */
	private function getPostTypes(): array
	{
		$postTypes = array_filter(
			get_post_types(['public' => true], 'objects'),
			static fn (WP_Post_Type $postTypeObject, string $postType) => ! in_array($postTypeObject->name, ['attachment'], true),
			ARRAY_FILTER_USE_BOTH,
		);

		return array_map(
			static fn (WP_Post_Type $postTypeObject): array => [
				'label' => $postTypeObject->label,
			],
			$postTypes,
		);
	}
}
