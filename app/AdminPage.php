<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;

use function sprintf;

use const PHP_INT_MAX;

class AdminPage implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addAction('admin_bar_menu', [$this, 'addInlineScript'], PHP_INT_MAX);
	}

	public function addInlineScript(): void
	{
		wp_print_inline_script_tag($this->getInlineScript());
	}

	private function getInlineScript(): string
	{
		return sprintf(
			<<<'SCRIPT'
			window.$syntatis = { featureFlipper: %s };
			SCRIPT,
			wp_json_encode(
				apply_filters('syntatis/feature_flipper/inline_data', [
					'themeSupport' => [
						'widgetsBlockEditor' => get_theme_support('widgets-block-editor'),
					],
					'environmentType' => wp_get_environment_type(),
				]),
			),
		);
	}
}
