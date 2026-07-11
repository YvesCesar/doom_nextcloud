<?php

declare(strict_types=1);

namespace OCA\Doom\Service;

use OCP\Security\ICredentialsManager;

/**
 * Stores the validated js-dos account (including the secret token) per user,
 * encrypted at rest through {@see ICredentialsManager}.
 */
class JsDosAccountService {
	private const CREDENTIALS_IDENTIFIER = 'doom_nextcloud_jsdos_account';

	public function __construct(
		private ICredentialsManager $credentialsManager,
	) {
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function get(string $userId): ?array {
		$value = $this->credentialsManager->retrieve($userId, self::CREDENTIALS_IDENTIFIER);

		return is_array($value) ? $value : null;
	}

	/**
	 * @param array<string, mixed> $account
	 */
	public function set(string $userId, array $account): void {
		$this->credentialsManager->store($userId, self::CREDENTIALS_IDENTIFIER, $account);
	}

	public function delete(string $userId): void {
		$this->credentialsManager->delete($userId, self::CREDENTIALS_IDENTIFIER);
	}
}
