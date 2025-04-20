<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper;

use ArrayAccess;
use BadMethodCallException;
use JsonSerializable;
use SSFV\Codex\Facades\App;
use Syntatis\FeatureFlipper\Helpers\Admin;
use WP_Post_Type;

use function array_filter;
use function array_map;
use function in_array;
use function is_string;

use const ARRAY_FILTER_USE_BOTH;

/** @phpstan-implements ArrayAccess<string,mixed> */
final class InlineData implements ArrayAccess, JsonSerializable
{
	/** @var array<string,mixed> */
	private array $data;

	public function __construct()
	{
		$this->data = [
			'$wp' => [
				'postTypes' => self::getPostTypes(),
				'themeSupport' => [
					'widgetsBlockEditor' => get_theme_support('widgets-block-editor'),
				],
			],
			'settingPage' => esc_url(Admin::url(App::name())),
			'settingPageTab' => sanitize_key(isset($_GET['tab']) && is_string($_GET['tab']) ? $_GET['tab'] : ''),
		];
	}

	public function offsetExists(mixed $offset): bool
	{
		if (is_string($offset)) {
			return isset($this->data[$offset]);
		}

		return false;
	}

	/** @return mixed $offset */
	public function offsetGet(mixed $offset): mixed
	{
		if (is_string($offset)) {
			return $this->data[$offset] ?? null;
		}

		return null;
	}

	public function offsetSet(mixed $offset, mixed $value): void
	{
		if (! is_string($offset) || $value === null) {
			return;
		}

		$this->data[$offset] = $value;
	}

	public function offsetUnset(mixed $offset): void
	{
		throw new BadMethodCallException('Cannot unset data');
	}

	/** @return array<string,mixed> */
	public function jsonSerialize(): array
	{
		/**
		 * For internal use. Subject to change. External plugin should not rely on this hook.
		 *
		 * @var self $instance
		 */
		$instance = apply_filters('syntatis/feature_flipper/inline_data', $this);

		return $instance->data;
	}

	/**
	 * Retrieve the list of registered post types on the site.
	 *
	 * @see register_post_type() for accepted arguments.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private static function getPostTypes(): array
	{
		$postTypes = array_filter(
			get_post_types(['public' => true], 'objects'),
			static fn (WP_Post_Type $postTypeObject, string $postType) => ! in_array($postTypeObject->name, ['attachment'], true),
			ARRAY_FILTER_USE_BOTH,
		);

		return array_map(
			static fn (WP_Post_Type $postTypeObject): array => [
				'name' => $postTypeObject->name,
				'label' => $postTypeObject->label,
				'supports' => get_all_post_type_supports($postTypeObject->name),
			],
			$postTypes,
		);
	}
}
