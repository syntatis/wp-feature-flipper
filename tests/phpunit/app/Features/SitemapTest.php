<?php

declare(strict_types=1);

namespace Syntatis\Tests\Features;

use SFFV\Codex\Foundation\Hooks\Hook;
use Syntatis\FeatureFlipper\Features\Sitemap;
use Syntatis\FeatureFlipper\Helpers\Option;
use Syntatis\FeatureFlipper\InlineData;
use Syntatis\Tests\WPTestCase;

/**
 * @group feature-sitemap
 * @group module-site
 */
final class SitemapTest extends WPTestCase
{
	private Hook $hook;
	private Sitemap $instance;

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function set_up(): void
	{
		parent::set_up();

		$this->hook = new Hook();
		$this->instance = new Sitemap();
		$this->instance->hook($this->hook);
	}

	// phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps -- WordPress convention.
	public function tear_down(): void
	{
		unset($_GET['tab']);

		parent::tear_down();
	}

	/** @testdox should have the inline data callback attached to hooks */
	public function testHook(): void
	{
		$this->assertSame(10, $this->hook->hasFilter('syntatis/feature_flipper/inline_data', [$this->instance, 'filterInlineData']));
	}

	/** @testdox should enable the built-in sitemap by default */
	public function testSitemapDefault(): void
	{
		$this->assertTrue(Option::isOn('sitemap'));
		$this->assertTrue(apply_filters('wp_sitemaps_enabled', true));
	}

	/** @testdox should disable the built-in sitemap when the option is off */
	public function testSitemapDisabledByOption(): void
	{
		Option::update('sitemap', false);

		$this->assertFalse(apply_filters('wp_sitemaps_enabled', true));
	}

	/** @testdox should not re-enable the built-in sitemap when a third-party plugin disabled it */
	public function testSitemapDisabledByThirdParty(): void
	{
		// Simulates an SEO plugin (e.g., Yoast SEO, RankMath) that ships its own sitemap.
		add_filter('wp_sitemaps_enabled', '__return_false', 10);

		$this->assertTrue(Option::isOn('sitemap'));
		$this->assertFalse(apply_filters('wp_sitemaps_enabled', true));
	}

	/** @testdox should add the Sitemap state to the inline data of the "site" tab */
	public function testFilterInlineData(): void
	{
		$_GET['tab'] = 'security';

		$data = $this->instance->filterInlineData(new InlineData());

		$this->assertFalse(isset($data['features']['sitemap']));

		$_GET['tab'] = 'site';

		$data = $this->instance->filterInlineData(new InlineData());

		$this->assertSame(
			['isManagedByOthers' => false],
			$data['features']['sitemap'],
		);
	}

	/** @testdox should flag the sitemap as managed by a third-party plugin */
	public function testFilterInlineDataManagedByThirdParty(): void
	{
		add_filter('wp_sitemaps_enabled', '__return_false', 10);

		$_GET['tab'] = 'site';

		$data = $this->instance->filterInlineData(new InlineData());

		$this->assertSame(
			['isManagedByOthers' => true],
			$data['features']['sitemap'],
		);
	}

	/** @testdox should not flag the sitemap when only Feature Flipper disabled it */
	public function testFilterInlineDataNotManagedByOption(): void
	{
		Option::update('sitemap', false);

		$_GET['tab'] = 'site';

		$data = $this->instance->filterInlineData(new InlineData());

		$this->assertSame(
			['isManagedByOthers' => false],
			$data['features']['sitemap'],
		);
	}
}
