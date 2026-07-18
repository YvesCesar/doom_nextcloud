<?php

declare(strict_types=1);

namespace OCA\Doom\Tests\Unit\Service;

use OCA\Doom\Service\JsDosClient;
use OCA\Doom\Service\JsDosUnavailableException;
use OCP\Http\Client\IClient;
use OCP\Http\Client\IClientService;
use OCP\Http\Client\IResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class JsDosClientTest extends TestCase {
	private IClientService&MockObject $clientService;
	private IClient&MockObject $client;
	private LoggerInterface&MockObject $logger;
	private JsDosClient $jsDosClient;

	protected function setUp(): void {
		parent::setUp();
		$this->clientService = $this->createMock(IClientService::class);
		$this->client = $this->createMock(IClient::class);
		$this->clientService->method('newClient')->willReturn($this->client);
		$this->logger = $this->createMock(LoggerInterface::class);
		$this->jsDosClient = new JsDosClient($this->clientService, $this->logger);
	}

	private function response(string $body): IResponse&MockObject {
		$response = $this->createMock(IResponse::class);
		$response->method('getBody')->willReturn($body);
		return $response;
	}

	public function testResolveAccountReturnsAccountForValidKey(): void {
		$this->client->expects($this->once())
			->method('get')
			->with('https://cloud.js-dos.com/token/get?id=abcde')
			->willReturn($this->response('{"email":"a@b.c","token":"abcde","premium":false,"success":true}'));

		$account = $this->jsDosClient->resolveAccount('abcde');

		// The transport-only "success" flag is stripped from the stored account.
		$this->assertSame(['email' => 'a@b.c', 'token' => 'abcde', 'premium' => false], $account);
	}

	public function testResolveAccountRejectsInvalidFormatWithoutHttpCall(): void {
		$this->client->expects($this->never())->method('get');

		$this->assertNull($this->jsDosClient->resolveAccount('ABCDE'));
		$this->assertNull($this->jsDosClient->resolveAccount('abcd'));
		$this->assertNull($this->jsDosClient->resolveAccount('abcdef'));
		$this->assertNull($this->jsDosClient->resolveAccount('abcd1'));
	}

	public function testResolveAccountReturnsNullWhenNoEmail(): void {
		$this->client->method('get')->willReturn($this->response('{}'));

		$this->assertNull($this->jsDosClient->resolveAccount('abcde'));
	}

	public function testResolveAccountReturnsNullWhenTokenNotFiveChars(): void {
		$this->client->method('get')->willReturn($this->response('{"email":"a@b.c","token":"toolong"}'));

		$this->assertNull($this->jsDosClient->resolveAccount('abcde'));
	}

	public function testResolveAccountThrowsAndLogsOnHttpError(): void {
		$this->client->method('get')->willThrowException(new \RuntimeException('network'));
		$this->logger->expects($this->once())->method('error');

		$this->expectException(JsDosUnavailableException::class);
		$this->jsDosClient->resolveAccount('abcde');
	}
}
