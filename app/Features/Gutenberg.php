<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Concerns\WithPostEditor;
use Syntatis\FeatureFlipper\Helpers\Option;

use function in_array;

use const PHP_INT_MAX;

class Gutenberg implements Hookable
{
	use WithHookName;
	use WithPostEditor;

	public function hook(Hook $hook): void
	{
		$hook->addFilter(self::optionName('gutenberg'), [$this, 'shouldActive'], PHP_INT_MAX);
		$hook->addFilter(self::defaultOptionName('gutenberg'), [$this, 'shouldActive'], PHP_INT_MAX);
		$hook->addFilter('use_block_editor_for_post', [$this, 'filterBlockEditorForPost'], PHP_INT_MAX);
	}

	/**
	 * Determine whether Gutenberg should be active for the given context.
	 *
	 * @param mixed $value The current value of the "gutenberg" option.
	 */
	public function shouldActive($value): bool
	{
		if (! self::isPostEditor()) {
			return (bool) $value;
		}

		return in_array(
			self::getPostEditorType(),
			(array) Option::get('gutenberg_post_types'),
			true,
		);
	}

	public function filterBlockEditorForPost(): bool
	{
		return (bool) Option::get('gutenberg');
	}
}
