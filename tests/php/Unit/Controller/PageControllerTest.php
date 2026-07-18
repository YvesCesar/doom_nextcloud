<?php

declare(strict_types=1);

namespace OCA\Doom\Tests\Unit\Controller;

use OCA\Doom\Controller\PageController;
use OCA\Doom\Service\JsDosAccountService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PageControllerTest extends TestCase {
	private const USER_ID = 'alice';

	private IRequest&MockObject $request;
	private JsDosAccountService&MockObject $accountService;
	private IInitialState&MockObject $initialState;

	protected function setUp(): void {
		parent::setUp();
		$this->request = $this->createMock(IRequest::class);
		$this->accountService = $this->createMock(JsDosAccountService::class);
		$this->initialState = $this->createMock(IInitialState::class);
	}

	public function testIndexProvidesStoredAccountAsInitialState(): void {
		$account = ['email' => 'a@b.c', 'token' => 'abcde'];
		$this->accountService->method('get')->with(self::USER_ID)->willReturn($account);
		$this->initialState->expects($this->once())
			->method('provideInitialState')
			->with('account', $account);
		$controller = new PageController(
			'doom_nextcloud',
			$this->request,
			self::USER_ID,
			$this->accountService,
			$this->initialState,
		);

		$response = $controller->index();

		$this->assertInstanceOf(TemplateResponse::class, $response);
		$this->assertSame('index', $response->getTemplateName());
		$this->assertSame('a@b.c', $response->getParams()['email']);
	}

	public function testIndexProvidesNullWhenNoUser(): void {
		$this->accountService->expects($this->never())->method('get');
		$this->initialState->expects($this->once())
			->method('provideInitialState')
			->with('account', null);
		$controller = new PageController(
			'doom_nextcloud',
			$this->request,
			null,
			$this->accountService,
			$this->initialState,
		);

		$controller->index();
	}
}
