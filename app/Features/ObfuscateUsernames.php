<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
use SSFV\Codex\Foundation\Hooks\Action;
use SSFV\Codex\Foundation\Hooks\Filter;
use SSFV\Codex\Foundation\Hooks\Hook;
use SSFV\Symfony\Component\Uid\Uuid;
use Syntatis\FeatureFlipper\Helpers\Option;
use WP_Query;
use WP_REST_Response;
use WP_User;
use WP_User_Query;

use function array_merge;
use function count;
use function is_array;
use function is_string;
use function sprintf;
use function str_replace;

use const PHP_INT_MAX;

final class ObfuscateUsernames implements Hookable
{
	private const USER_UUID_META_KEY = '_syntatis_uuid';

	public function hook(Hook $hook): void
	{
		$hook->parse($this);
		$hook->addAction(
			Option::hook('add:obfuscate_usernames'),
			static function (string $optionName, mixed $value): void {
				if ((bool) $value !== true) {
					return;
				}

				self::addUuid();
			},
			10,
			2,
		);
		$hook->addAction(
			Option::hook('update:obfuscate_usernames'),
			static function (mixed $oldValue, mixed $value): void {
				$oldValue = (bool) $oldValue;
				$value = (bool) $value;

				if ($value !== true || $oldValue === $value) {
					return;
				}

				self::addUuid();
			},
			10,
			2,
		);
	}

	#[Action(name: 'pre_get_posts', priority: PHP_INT_MAX)]
	public function preGetPosts(WP_Query $query): void
	{
		if (! Option::isOn('obfuscate_usernames')) {
			return;
		}

		/**
		 * Only run this filter on the front end and if the query is for an author.
		 */
		if (! $query->is_author()) {
			return;
		}

		/**
		 * If the permalink structure is set to plain, the author should be queried
		 * by the user ID.
		 */
		if ((bool) get_option('permalink_structure') === false) {
			return;
		}

		// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress Core convention.
		$authorName = $query->query_vars['author_name'] ?? '';

		if (! is_string($authorName) || ! Uuid::isValid($authorName)) {
			self::setNotFound($query);

			return;
		}

		$users = get_users([
			'meta_key' => self::USER_UUID_META_KEY,
			'meta_value' => $authorName,
		]);

		if (count($users) <= 0) {
			self::setNotFound($query);

			return;
		}

		$user = $users[0];

		if (! $user instanceof WP_User) {
			self::setNotFound($query);

			return;
		}

		$query->set('author_name', $user->user_nicename);
		// phpcs:enable
	}

	#[Filter(name: 'rest_prepare_user', priority: PHP_INT_MAX)]
	public function restPrepareUser(WP_REST_Response $response, WP_User $user): WP_REST_Response
	{
		if (! Option::isOn('obfuscate_usernames')) {
			return $response;
		}

		$data = $response->get_data();

		if (is_array($data)) {
			$slug = self::getUuid($user);

			if (is_string($slug)) {
				$data['slug'] = $slug;
			}
		}

		$response->set_data($data);

		return $response;
	}

	#[Filter(name: 'author_link', priority: PHP_INT_MAX)]
	public function authorLink(string $link, int $userId, string $authorSlug): string
	{
		if (! Option::isOn('obfuscate_usernames')) {
			return $link;
		}

		/**
		 * Only change the author link when the permalink structure is not set to
		 * plain.
		 */
		if ((bool) get_option('permalink_structure') === false) {
			return $link;
		}

		$uuid = self::getUuid(new WP_User($userId));

		if (is_string($uuid)) {
			return str_replace('/' . $authorSlug, '/' . $uuid, $link);
		}

		return $link;
	}

	/**
	 * @param array<string,mixed> $customMeta Array of custom user meta values keyed by meta key.
	 *
	 * @return array<string,mixed>
	 */
	#[Filter(name: 'insert_custom_user_meta', priority: PHP_INT_MAX)]
	public function insertCustomUserMeta(array $customMeta, WP_User $user): array
	{
		$uuid = self::getUuid($user);

		if (is_string($uuid)) {
			return $customMeta;
		}

		return array_merge(
			$customMeta,
			[
				self::USER_UUID_META_KEY => self::generateUuid($user),
			],
		);
	}

	private static function addUuid(): void
	{
		foreach (self::getUsers() as $user) {
			if (! $user instanceof WP_User) {
				continue;
			}

			add_user_meta(
				$user->ID,
				self::USER_UUID_META_KEY,
				self::generateUuid($user),
				true,
			);
		}
	}

	/**
	 * Retrieve the list of users which currently does not have the UUID.
	 *
	 * @return array<mixed>
	 */
	private static function getUsers(): array
	{
		return (new WP_User_Query([
			'meta_query' => [
				[
					'key'     => self::USER_UUID_META_KEY,
					'compare' => 'NOT EXISTS', // Explicitly check for absence of the meta key
				],
			],
			'fields' => 'all', // Return full user objects
		]))->get_results();
	}

	/** @phpstan-return non-empty-string|null */
	private static function getUuid(WP_User $user): string|null
	{
		$uuid = get_user_meta($user->ID, self::USER_UUID_META_KEY, true);

		if (is_string($uuid) && $uuid !== '' && Uuid::isValid($uuid)) {
			return $uuid;
		}

		return null;
	}

	private static function generateUuid(WP_User $user): string
	{
		return (string) Uuid::v5(
			Uuid::fromString(Uuid::NAMESPACE_URL),
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			sprintf('%s:%s:%s', $user->ID, $user->user_login, $user->user_email),
		);
	}

	private static function setNotFound(WP_Query $query): void
	{
		// phpcs:disable Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps -- WordPress Core convention.
		$query->is_404 = true;
		$query->is_author = false;
		$query->is_archive = false;
		// phpcs:enable
	}
}
