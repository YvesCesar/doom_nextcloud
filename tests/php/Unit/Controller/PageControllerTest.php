<?php

declare(strict_types=1);

namespace OCA\Doom\Tests\Unit\Controller;

use OCA\Doom\Controller\PageController;
use OCA\Doom\Service\JsDosAccountService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IRequest;
use OCP\IURLGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PageControllerTest extends TestCase {
	private const USER_ID = 'alice';
	private const SETTINGS_URL = '/settings/user/doom_nextcloud';

	private IRequest&MockObject $request;
	private JsDosAccountService&MockObject $accountService;
	private IInitialState&MockObject $initialState;
	private IURLGenerator&MockObject $urlGenerator;

	protected function setUp(): void {
		parent::setUp();
		$this->request = $this->createMock(IRequest::class);
		$this->accountService = $this->createMock(JsDosAccountService::class);
		$this->initialState = $this->createMock(IInitialState::class);
		$this->urlGenerator = $this->createMock(IURLGenerator::class);
		$this->urlGenerator->method('linkToRoute')
			->with('settings.PersonalSettings.index', ['section' => 'doom_nextcloud'])
			->willReturn(self::SETTINGS_URL);
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
			$this->urlGenerator,
		);

		$response = $controller->index();

		$this->assertInstanceOf(TemplateResponse::class, $response);
		$this->assertSame('index', $response->getTemplateName());
		$this->assertSame('a@b.c', $response->getParams()['email']);
		$this->assertSame(self::SETTINGS_URL, $response->getParams()['settingsUrl']);
	}

	public function testIndexProvidesEmptyAccountWhenNoUser(): void {
		$this->accountService->expects($this->never())->method('get');
		$this->initialState->expects($this->once())
			->method('provideInitialState')
			->with('account', []);
		$controller = new PageController(
			'doom_nextcloud',
			$this->request,
			null,
			$this->accountService,
			$this->initialState,
			$this->urlGenerator,
		);

		$controller->index();
	}

	public function testIndexProvidesEmptyAccountWhenUserHasNoStoredAccount(): void {
		$this->accountService->expects($this->once())
			->method('get')
			->with(self::USER_ID)
			->willReturn(null);
		$this->initialState->expects($this->once())
			->method('provideInitialState')
			->with('account', []);
		$controller = new PageController(
			'doom_nextcloud',
			$this->request,
			self::USER_ID,
			$this->accountService,
			$this->initialState,
			$this->urlGenerator,
		);

		$controller->index();
	}
}
