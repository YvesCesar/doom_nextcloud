<?php

declare(strict_types=1);

namespace OCA\Doom\Controller;

use OCA\Doom\AppInfo\Application;
use OCA\Doom\Service\JsDosAccountService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\OpenAPI;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Services\IInitialState;
use OCP\IRequest;
use OCP\IURLGenerator;

/**
 * @psalm-suppress UnusedClass
 */
class PageController extends Controller
{
	public function __construct(
		string $appName,
		IRequest $request,
		private ?string $userId,
		private JsDosAccountService $accountService,
		private IInitialState $initialState,
		private IURLGenerator $urlGenerator,
	) {
		parent::__construct($appName, $request);
	}

	#[NoCSRFRequired]
	#[NoAdminRequired]
	#[OpenAPI(OpenAPI::SCOPE_IGNORE)]
	public function index(): TemplateResponse
	{
		// Expose the stored account so it can be written to localStorage before
		// js-dos boots (js-dos reads its account at script-eval time).
		$account = $this->userId !== null ? $this->accountService->get($this->userId) : null;
		$this->initialState->provideInitialState('account', $account ?? []);

		$response = new TemplateResponse(
			Application::APP_ID,
			'index',
			[
				'email' => $account['email'] ?? null,
				'settingsUrl' => $this->urlGenerator->linkToRoute(
					'settings.PersonalSettings.index',
					['section' => Application::APP_ID],
				),
			],
		);
		$csp = new ContentSecurityPolicy();
		$csp->addAllowedScriptDomain('blob:');
		$csp->addAllowedWorkerSrcDomain('blob:');
		$csp->allowEvalWasm();
		$csp->allowEvalScript();
		$response->setContentSecurityPolicy($csp);
		return $response;
	}
}
