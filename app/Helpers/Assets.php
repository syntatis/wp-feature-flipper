<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Helpers;

use SSFV\Codex\Facades\App;
use Syntatis\FeatureFlipper\Concerns\DontInstantiate;

use function array_filter;
use function array_map;
use function array_values;
use function is_array;
use function is_readable;
use function is_string;
use function str_ends_with;

final class Assets
{
	use DontInstantiate;

	/**
	 * @param string $path The path to the PHP file containing the asset manifests (version, dependencies).
	 * @phpstan-param non-empty-string $path
	 *
	 * @return array{version:string|null,dependencies:array<string>}
	 */
	public static function manifest(string $path): array
	{
		$path = App::dir($path);
		$assets = str_ends_with($path, '.php') && is_readable($path) ? require $path : [];
		$assets = is_array($assets) ? $assets : [];

		$version = isset($assets['version']) && is_string($assets['version']) ? sanitize_key($assets['version']) : null;
		$dependencies = isset($assets['dependencies']) && is_array($assets['dependencies']) ?
			self::sanitizeDependencies($assets['dependencies']) :
			[];

		return [
			'version' => $version,
			'dependencies' => $dependencies,
		];
	}

	/**
	 * @param array<mixed> $dependencies Unsanitized dependencies, retrieved from built file.
	 *
	 * @phpstan-return list<string>
	 */
	private static function sanitizeDependencies(array $dependencies): array
	{
		$filtered = array_filter($dependencies, static fn ($dep) => is_string($dep) && $dep !== '');

		return array_values(array_map('sanitize_key', $filtered));
	}
}
