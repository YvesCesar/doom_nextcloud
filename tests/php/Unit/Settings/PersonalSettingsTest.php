<?php

declare(strict_types=1);

namespace OCA\Doom\Tests\Unit\Settings;

use OCA\Doom\Service\JsDosAccountService;
use OCA\Doom\Settings\PersonalSettings;
use OCP\AppFramework\Http\TemplateResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PersonalSettingsTest extends TestCase {
	private const USER_ID = 'alice';

	private JsDosAccountService&MockObject $accountService;

	protected function setUp(): void {
		parent::setUp();
		$this->accountService = $this->createMock(JsDosAccountService::class);
	}

	public function testGetFormRendersPersonalTemplateWithStoredEmail(): void {
		$this->accountService->method('get')
			->with(self::USER_ID)
			->willReturn(['email' => 'a@b.c', 'token' => 'abcde']);
		$settings = new PersonalSettings(self::USER_ID, $this->accountService);

		$form = $settings->getForm();

		$this->assertInstanceOf(TemplateResponse::class, $form);
		$this->assertSame('doom_nextcloud', $form->getApp());
		$this->assertSame('personal-settings', $form->getTemplateName());
		$this->assertSame('a@b.c', $form->getParams()['email']);
	}

	public function testGetFormWithoutAccountPassesNullEmail(): void {
		$this->accountService->method('get')->with(self::USER_ID)->willReturn(null);
		$settings = new PersonalSettings(self::USER_ID, $this->accountService);

		$this->assertNull($settings->getForm()->getParams()['email']);
	}

	public function testSectionAndPriority(): void {
		$settings = new PersonalSettings(self::USER_ID, $this->accountService);

		$this->assertSame('doom_nextcloud', $settings->getSection());
		$this->assertIsInt($settings->getPriority());
	}
}
