<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SFFV\Codex\Contracts\Hookable;
use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Helpers\Option;

use function add_option;
use function delete_option;
use function get_option;
use function in_array;
use function update_option;

/**
 * Runs one-time upgrades when the plugin is updated.
 *
 * Each migration is self-contained and gated on the legacy data it cleans up,
 * so it only runs once and is a no-op on fresh installs.
 */
final class Upgrade implements Hookable
{
	public function hook(Hook $hook): void
	{
		$hook->addAction('plugins_loaded', [$this, 'run']);
	}

	/**
	 * Run any pending migrations.
	 */
	public function run(): void
	{
		$this->migrateRevisionsMax();
	}

	/**
	 * Migrate the legacy `revisions_max_enabled` option to the `revisions_max`
	 * value.
	 *
	 * The `revisions_max_enabled` previously decided whether to cap the number
	 * of revisions. The cap is now expressed directly with `revisions_max`,
	 * where a value below 1 means unlimited — the previous default.
	 *
	 * Sites that never enabled the cap keep unlimited revisions, while sites
	 * that enabled it keep their configured value (defaulting to 5, the
	 * previous default, when no value was stored).
	 */
	private function migrateRevisionsMax(): void
	{
		$legacy = get_option(Option::name('revisions_max_enabled'), 'unset');

		if ($legacy === 'unset') {
			return;
		}

		$enabled = in_array($legacy, [true, '1'], true);

		if ($enabled) {
			// Preserve the previous default of 5 when no value was stored.
			// "add_option" leaves any already-stored value untouched.
			add_option(Option::name('revisions_max'), 5);
		} else {
			update_option(Option::name('revisions_max'), -1);
		}

		delete_option(Option::name('revisions_max_enabled'));
	}
}
