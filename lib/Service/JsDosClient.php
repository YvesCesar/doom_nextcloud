<?php

declare(strict_types=1);

namespace OCA\Doom\Service;

use OCP\Http\Client\IClientService;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Resolves a js-dos account from a key against the js-dos cloud API.
 *
 * The emulator itself cannot do this when self-hosted: its browser request to
 * cloud.js-dos.com is blocked by CORS (the endpoint only allows the js-dos.com
 * origins). Doing it server-side sidesteps that, and mirrors the emulator's own
 * validation (5 lowercase letters, response must carry an email and a 5-char
 * token).
 */
class JsDosClient {
	private const TOKEN_URL = 'https://cloud.js-dos.com/token/get?id=';

	public function __construct(
		private IClientService $clientService,
		private LoggerInterface $logger,
	) {
	}

	/**
	 * @return array<string, mixed>|null the account, or null if the key is
	 *                                   malformed or not tied to an account
	 * @throws JsDosUnavailableException if the js-dos API cannot be reached
	 */
	public function resolveAccount(string $key): ?array {
		if (!preg_match('/^[a-z]{5}$/', $key)) {
			return null;
		}

		try {
			$response = $this->clientService->newClient()->get(self::TOKEN_URL . $key);
			$account = json_decode((string)$response->getBody(), true);
		} catch (Throwable $e) {
			$this->logger->error('Could not reach the js-dos API to validate a key', ['exception' => $e]);
			throw new JsDosUnavailableException('js-dos API unreachable', 0, $e);
		}

		if (!is_array($account)
			|| empty($account['email'])
			|| !isset($account['token'])
			|| !is_string($account['token'])
			|| strlen($account['token']) !== 5
		) {
			return null;
		}

		unset($account['success']);

		return $account;
	}
}
