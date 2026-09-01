<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Modules;

use IteratorAggregate;
use SFFV\Codex\Contracts\Extendable;
use SFFV\Psr\Container\ContainerInterface;
use Syntatis\FeatureFlipper\Contracts\InlineDataProvider;
use Syntatis\FeatureFlipper\InlineDataCollection;
use Traversable;

use function is_object;

/** @phpstan-implements IteratorAggregate<object> */
final class Modules implements IteratorAggregate
{
	private ContainerInterface $container;

	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}

	/** @return Traversable<object> */
	public function getIterator(): Traversable
	{
		yield from $this->iterate($this->getModules());
	}

	/** @return iterable<object> */
	private function getModules(): iterable
	{
		yield new Admin();
		yield new Advanced();
		yield new General();
		yield new Media();
		yield new Security();
		yield new Site();
	}

	/**
	 * @param iterable<mixed> $values The value to iterate.
	 *
	 * @return iterable<object>
	 */
	private function iterate(iterable $values): iterable
	{
		foreach ($values as $value) {
			if (! is_object($value)) {
				continue;
			}

			if ($value instanceof InlineDataProvider) {
				/** @var InlineDataCollection $collection */
				$collection = $this->container->get(InlineDataCollection::class);
				$collection->add($value);
			}

			yield $value;

			if (! ($value instanceof Extendable)) {
				continue;
			}

			yield from $this->iterate($value->getInstances($this->container));
		}
	}
}
