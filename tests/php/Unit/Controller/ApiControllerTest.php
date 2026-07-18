<?php

declare(strict_types=1);

namespace OCA\Doom\Tests\Unit\Controller;

use OCA\Doom\Controller\ApiController;
use OCA\Doom\Service\JsDosAccountService;
use OCA\Doom\Service\JsDosClient;
use OCA\Doom\Service\JsDosUnavailableException;
use OCP\AppFramework\Http;
use OCP\IRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ApiControllerTest extends TestCase {
	private const USER_ID = 'alice';

	private IRequest&MockObject $request;
	private JsDosAccountService&MockObject $accountService;
	private JsDosClient&MockObject $jsDosClient;
	private ApiController $controller;

	protected function setUp(): void {
		parent::setUp();
		$this->request = $this->createMock(IRequest::class);
		$this->accountService = $this->createMock(JsDosAccountService::class);
		$this->jsDosClient = $this->createMock(JsDosClient::class);
		$this->controller = new ApiController(
			'doom_nextcloud',
			$this->request,
			self::USER_ID,
			$this->accountService,
			$this->jsDosClient,
		);
	}

	public function testSetKeyValidatesStoresAndReturnsAccount(): void {
		$account = ['email' => 'a@b.c', 'token' => 'abcde', 'premium' => false];
		$this->jsDosClient->method('resolveAccount')->with('abcde')->willReturn($account);
		$this->accountService->expects($this->once())
			->method('set')
			->with(self::USER_ID, $account);

		$response = $this->controller->setKey('abcde');

		$this->assertSame(Http::STATUS_OK, $response->getStatus());
		$this->assertSame(['account' => $account], $response->getData());
	}

	public function testSetKeyRejectsInvalidKey(): void {
		$this->jsDosClient->method('resolveAccount')->with('nope')->willReturn(null);
		$this->accountService->expects($this->never())->method('set');

		$response = $this->controller->setKey('nope');

		$this->assertSame(Http::STATUS_UNPROCESSABLE_ENTITY, $response->getStatus());
	}

	public function testSetKeyReturnsServiceUnavailableWhenJsDosUnreachable(): void {
		$this->jsDosClient->method('resolveAccount')
			->willThrowException(new JsDosUnavailableException());
		$this->accountService->expects($this->never())->method('set');

		$response = $this->controller->setKey('abcde');

		$this->assertSame(Http::STATUS_SERVICE_UNAVAILABLE, $response->getStatus());
	}

	public function testDeleteStateDeletes(): void {
		$this->accountService->expects($this->once())
			->method('delete')
			->with(self::USER_ID);

		$response = $this->controller->deleteState();

		$this->assertSame(Http::STATUS_OK, $response->getStatus());
		$this->assertSame(['status' => 'ok'], $response->getData());
	}

	public function testSetKeyReturnsUnauthorizedWithoutUser(): void {
		$controller = new ApiController('doom_nextcloud', $this->request, null, $this->accountService, $this->jsDosClient);
		$this->jsDosClient->expects($this->never())->method('resolveAccount');

		$response = $controller->setKey('abcde');

		$this->assertSame(Http::STATUS_UNAUTHORIZED, $response->getStatus());
	}
}
