<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Facades\App;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithPostTypes;

use function sprintf;

use const PHP_INT_MAX;

class CommonScripts implements Hookable
{
	use WithPostTypes;

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
					'wp' => [
						'environmentType' => wp_get_environment_type(),
						'postTypes' => self::getPostTypes(),
						'themeSupport' => [
							'widgetsBlockEditor' => get_theme_support('widgets-block-editor'),
						],
					],
				]),
			),
		);
	}
}
