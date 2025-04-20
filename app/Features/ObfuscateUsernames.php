<?php

declare(strict_types=1);

namespace Syntatis\FeatureFlipper\Features;

use SSFV\Codex\Contracts\Hookable;
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
		$hook->addAction(
			Option::hook('add:obfuscate_usernames'),
			static function (string $optionName, $value): void {
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
			static function ($oldValue, $value): void {
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

		if (! Option::isOn('obfuscate_usernames')) {
			return;
		}

		$hook->addAction('pre_get_posts', [$this, 'preGetPosts'], PHP_INT_MAX);
		$hook->addFilter('author_link', [$this, 'filterAuthorLink'], PHP_INT_MAX, 3);
		$hook->addFilter('insert_custom_user_meta', [$this, 'filterInsertCustomUserMeta'], PHP_INT_MAX, 2);
		$hook->addFilter('rest_prepare_user', [$this, 'filterRestPrepareUser'], PHP_INT_MAX, 2);
	}

	public function preGetPosts(WP_Query $query): void
	{
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

	public function filterRestPrepareUser(WP_REST_Response $response, WP_User $user): WP_REST_Response
	{
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

	public function filterAuthorLink(string $link, int $userId, string $authorSlug): string
	{
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
	public function filterInsertCustomUserMeta(array $customMeta, WP_User $user): array
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
