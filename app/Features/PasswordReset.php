<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;

final class PasswordReset implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addFilter('allow_password_reset', '__return_false');
		$hook->addFilter('gettext', [self::class, 'removeLostPasswordText']);
	}

	public static function removeLostPasswordText(string $text): string
	{
		if ($text === 'Lost your password?') {
			$text = '';
		}

		return $text;
	}
}
