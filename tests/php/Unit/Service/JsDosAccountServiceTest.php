<?php

declare(strict_types=1);

namespace OCA\Doom\Tests\Unit\Service;

use OCA\Doom\Service\JsDosAccountService;
use OCP\Security\ICredentialsManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class JsDosAccountServiceTest extends TestCase {
	private const IDENTIFIER = 'doom_nextcloud_jsdos_account';
	private const USER_ID = 'alice';

	private ICredentialsManager&MockObject $credentialsManager;
	private JsDosAccountService $service;

	protected function setUp(): void {
		parent::setUp();
		$this->credentialsManager = $this->createMock(ICredentialsManager::class);
		$this->service = new JsDosAccountService($this->credentialsManager);
	}

	public function testGetReturnsNullWhenNothingStored(): void {
		$this->credentialsManager->method('retrieve')
			->with(self::USER_ID, self::IDENTIFIER)
			->willReturn(null);

		$this->assertNull($this->service->get(self::USER_ID));
	}

	public function testGetReturnsStoredAccount(): void {
		$account = ['email' => 'a@b.c', 'token' => 'abcde', 'premium' => false];
		$this->credentialsManager->method('retrieve')
			->with(self::USER_ID, self::IDENTIFIER)
			->willReturn($account);

		$this->assertSame($account, $this->service->get(self::USER_ID));
	}

	public function testSetStoresAccount(): void {
		$account = ['email' => 'a@b.c', 'token' => 'abcde'];
		$this->credentialsManager->expects($this->once())
			->method('store')
			->with(self::USER_ID, self::IDENTIFIER, $account);

		$this->service->set(self::USER_ID, $account);
	}

	public function testDeleteRemovesCredentials(): void {
		$this->credentialsManager->expects($this->once())
			->method('delete')
			->with(self::USER_ID, self::IDENTIFIER);

		$this->service->delete(self::USER_ID);
	}
}
