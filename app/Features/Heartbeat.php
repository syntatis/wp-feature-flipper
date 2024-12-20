<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Concerns\HasHookName;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManageAdmin;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManageFront;
use Syntatis\FeatureFlipper\Features\Heartbeat\ManagePostEditor;
use Syntatis\FeatureFlipper\Helpers\Option;

use const PHP_INT_MAX;

/**
 * Manage WordPress Heartbeat API feature.
 *
 * The WordPress Heartbeat API is a core feature in WordPress that enables
 * near real-time communication between a web browser and the server.
 *
 * It uses AJAX (Asynchronous JavaScript and XML) to send regular "pulses"
 * or "ticks" between the browser and the server, allowing for data
 * exchange and triggering events.
 *
 * @see https://developer.wordpress.org/plugins/javascript/heartbeat-api/
 */
class Heartbeat implements Hookable, Extendable
{
	use HasHookName;

	/**
	 * Whether the Heartbeat API is enabled.
	 */
	private bool $heartbeat;

	public function __construct()
	{
		$this->heartbeat = (bool) Option::get('heartbeat');
	}

	public function hook(Hook $hook): void
	{
		$hook->addAction('init', [$this, 'deregisterScripts'], PHP_INT_MAX);

		/**
		 * The Heartbeat API uses AJAX to send requests to the server at intervals.
		 * By default, this is every 15 seconds in the post editor and every 60
		 * seconds on the dashboard, while a user is logged into the WordPress
		 * admin.
		 *
		 * These filters manage the options that control these intervals. They set
		 * a cascading effect in which, if the corresponding option is off, the
		 * corresponding interval will return a `null`, effectively disabling
		 * the Heartbeat API for that specific context.
		 */

		$onGlobal = fn ($value) => $this->heartbeat ? $value : $this->heartbeat;
		$onAdminPages = fn ($value) => (bool) Option::get('heartbeat_admin') && $this->heartbeat ? $value : null;
		$onFrontPages = fn ($value) => (bool) Option::get('heartbeat_front') && $this->heartbeat ? $value : null;
		$onPostEditor = fn ($value) => (bool) Option::get('heartbeat_post_editor') && $this->heartbeat ? $value : null;

		// Manage the "heartbeat_admin" and "heartbeat_admin_interval" options.
		$hook->addFilter(self::optionName('heartbeat_admin'), $onGlobal);
		$hook->addFilter(self::defaultOptionName('heartbeat_admin'), $onGlobal);
		$hook->addFilter(
			self::optionName('heartbeat_admin_interval'),
			$onAdminPages,
		);
		$hook->addFilter(
			self::defaultOptionName('heartbeat_admin_interval'),
			$onAdminPages,
		);

		// Manage the "heartbeat_front" and "heartbeat_front_interval" options.
		$hook->addFilter(self::optionName('heartbeat_front'), $onGlobal);
		$hook->addFilter(self::defaultOptionName('heartbeat_front'), $onGlobal);
		$hook->addFilter(
			self::optionName('heartbeat_front_interval'),
			$onFrontPages,
		);
		$hook->addFilter(
			self::defaultOptionName('heartbeat_front_interval'),
			$onFrontPages,
		);

		// Manage the "heartbeat_post_editor" and "heartbeat_post_editor_interval" options.
		$hook->addFilter(self::optionName('heartbeat_post_editor'), $onGlobal);
		$hook->addFilter(self::defaultOptionName('heartbeat_post_editor'), $onGlobal);
		$hook->addFilter(
			self::optionName('heartbeat_post_editor_interval'),
			$onPostEditor,
		);
		$hook->addFilter(
			self::defaultOptionName('heartbeat_post_editor_interval'),
			$onPostEditor,
		);
	}

	public function deregisterScripts(): void
	{
		if ($this->heartbeat) {
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
		yield new ManageFront();
		yield new ManagePostEditor();
	}
}
