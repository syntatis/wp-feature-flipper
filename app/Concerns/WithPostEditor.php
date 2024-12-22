<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Concerns;

use function is_numeric;
use function is_string;

trait WithPostEditor
{
	private static function isPostEditor(): bool
	{
		$pagenow = $GLOBALS['pagenow'] ?? '';

		return is_admin() && ($pagenow === 'post.php' || $pagenow === 'post-new.php');
	}

	private static function getPostEditorType(): ?string
	{
		if (! self::isPostEditor()) {
			return null;
		}

		$arg = $GLOBALS['typenow'] ?? null;

		if ($arg === null || $arg === '') {
			$arg = $_GET['post'] ?? null;
		}

		if (is_numeric($arg)) {
			$postType = get_post_type(absint($arg));

			return ! is_string($postType) || $postType === '' ? null : $postType;
		}

		return is_string($arg) ? $arg : null;
	}
}
