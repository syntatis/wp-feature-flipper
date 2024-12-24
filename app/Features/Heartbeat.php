<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Concerns\WithHookName;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManageAdmin;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManagePostEditor;
use Syntatis\FeatureFlipper\Helpers\Option;

use const PHP_INT_MAX;

/**
 * Manage WordPress Heartbeat API feature.
 *
 * The WordPress Heartbeat API is a core feature in WordPress that enables
 * near real-time communication between a web browser and the server.
 *
 * The Heartbeat API uses AJAX to send requests to the server at intervals.
 * By default, this is every 15 seconds in the post editor and every 60
 * seconds on the dashboard, while a user is logged into the WordPress
 * admin.
 *
 * @see https://developer.wordpress.org/plugins/javascript/heartbeat-api/
 */
class Heartbeat implements Hookable, Extendable
{
	use WithHookName;

	public function hook(Hook $hook): void
	{
		$hook->addAction('init', [$this, 'deregisterScripts'], PHP_INT_MAX);
	}

	public function deregisterScripts(): void
	{
		if ((bool) Option::get('heartbeat')) {
			return;
		}

		/**
		 * If the feature is disabled, deregister the Heartbeat API script, which
		 * effectively stopping all the Heartbeat API requests on all pages.
		 */
		wp_deregister_script('heartbeat');
	}

	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		yield new ManageAdmin();
		yield new ManagePostEditor();
	}
}
