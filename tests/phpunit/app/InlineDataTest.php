<?php

declare(strict_types=1);

namespace Syntatis\Tests;

use ArrayAccess;
use Syntatis\FeatureFlipper\Contracts\InlineDataProvider;
use Syntatis\FeatureFlipper\InlineData;

class InlineDataTest extends WPTestCase
{
	private InlineData $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->instance = new InlineData();
	}

	/** @testdox should return the site url */
	public function testGetOffsetSiteURL(): void
	{
		$this->assertEquals(get_site_url(), $this->instance['$wp']['siteUrl']);
	}

	/** @testdox should run the registered providers when serialized */
	public function testSerializeRunsProviders(): void
	{
		$provider = new class implements InlineDataProvider {
			public function inlineData(ArrayAccess $data): ArrayAccess
			{
				$data['custom'] = 'value';

				return $data;
			}
		};

		$data = (new InlineData([$provider]))->jsonSerialize();

		$this->assertSame('value', $data['custom']);
	}
}
