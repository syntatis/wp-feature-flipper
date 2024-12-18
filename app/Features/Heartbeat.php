<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Concerns\WPFilterNames;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManageAdmin;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManageFront;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManagePostEdit;
use Syntatis\FeatureFlipper\Helpers\Option;

class Heartbeat implements Hookable, Extendable
{
	use WPFilterNames;

	public function hook(Hook $hook): void
	{
		$hook->addFilter(
			self::optionFilterName('heartbeat_admin_interval'),
			static fn ($value) => (bool) Option::get('heartbeat_admin') ? $value : 0,
		);

		$hook->addFilter(
			self::optionFilterName('heartbeat_front_interval'),
			static fn ($value) => (bool) Option::get('heartbeat_front') ? $value : 0,
		);

		$hook->addFilter(
			self::optionFilterName('heartbeat_post_edit_interval'),
			static fn ($value) => (bool) Option::get('heartbeat_post_edit') ? $value : 0,
		);
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new ManageAdmin();
		yield new ManageFront();
		yield new ManagePostEdit();
	}
}
