<?php

declare(strict_types=1);

namespace OCA\Doom\Settings;

use OCA\Doom\AppInfo\Application;
use OCA\Doom\Service\JsDosAccountService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;

class PersonalSettings implements ISettings {
	public function __construct(
		private ?string $userId,
		private JsDosAccountService $accountService,
	) {
	}

	public function getForm(): TemplateResponse {
		$account = $this->userId !== null ? $this->accountService->get($this->userId) : null;

		return new TemplateResponse(Application::APP_ID, 'personal-settings', [
			'email' => $account['email'] ?? null,
		]);
	}

	public function getSection(): string {
		return Application::APP_ID;
	}

	public function getPriority(): int {
		return 50;
	}
}
