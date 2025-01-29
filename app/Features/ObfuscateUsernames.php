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
		$authorName = $query->query_vars['author_name'];

		if (! Uuid::isValid($authorName)) {
			$query->is_404 = true;
			$query->is_author = false;
			$query->is_archive = false;

			return;
		}

		$users = get_users([
			'meta_key' => self::USER_UUID_META_KEY,
			'meta_value' => $authorName,
		]);

		if (count($users) <= 0) {
			$query->is_404 = true;
			$query->is_author = false;
			$query->is_archive = false;

			return;
		}

		$query->set('author_name', $users[0]->user_nicename);
		// phpcs:enable
	}

	public function filterRestPrepareUser(WP_REST_Response $response, WP_User $user): WP_REST_Response
	{
		$data = $response->get_data();

		if (is_array($data)) {
			$data['slug'] = get_user_meta($user->ID, self::USER_UUID_META_KEY, true);
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

		$uuid = get_user_meta($userId, self::USER_UUID_META_KEY, true);

		if (is_string($uuid) && Uuid::isValid($uuid)) {
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
			$id = $user->ID;

			add_user_meta(
				$id,
				self::USER_UUID_META_KEY,
				(string) Uuid::v5(
					Uuid::fromString(Uuid::NAMESPACE_URL),
                    // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
					sprintf('%s:%s:%s', $id, $user->user_login, $user->user_email),
				),
				true,
			);
		}
	}

	/**
	 * Retrieve the list of users which currently does not have the UUID.
	 *
	 * @return array<WP_User>
	 */
	private static function getUsers(): iterable
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
}
