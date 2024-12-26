<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use SSFV\Codex\Contracts\Extendable;
use SSFV\Codex\Settings\Settings;
use SSFV\Psr\Container\ContainerInterface;

use function is_object;

class Plugin implements Extendable
{
	/** @return iterable<object> */
	public function getInstances(ContainerInterface $container): iterable
	{
		/** @var Settings $settings */
		$settings = $container->get(Settings::class);

		yield new CommonScripts();
		yield new SettingPage($settings);
		yield from $this->iterate($this->getModules(), $container);

		// Mark as initialized.
		do_action('syntatis/feature_flipper/init', $container);
	}

	/** @return iterable<object> */
	private function getModules(): iterable
	{
		yield new Modules\Admin();
		yield new Modules\Advanced();
		yield new Modules\General();
		yield new Modules\Media();
		yield new Modules\Security();
		yield new Modules\Site();
	}

	/**
	 * @param iterable<mixed> $values The value to iterate.
	 *
	 * @return iterable<object>
	 */
	private function iterate(iterable $values, ContainerInterface $container): iterable
	{
		foreach ($values as $value) {
			if (! is_object($value)) {
				continue;
			}

			yield $value;

			if (! ($value instanceof Extendable)) {
				continue;
			}

			yield from $this->iterate($value->getInstances($container), $container);
		}
	}
}
