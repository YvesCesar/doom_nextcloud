<?php

declare(strict_types=1);

namespace OCA\Doom\Tests\Unit\Settings;

use OCA\Doom\Settings\PersonalSection;
use OCP\IL10N;
use OCP\IURLGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PersonalSectionTest extends TestCase {
	private IL10N&MockObject $l10n;
	private IURLGenerator&MockObject $urlGenerator;
	private PersonalSection $section;

	protected function setUp(): void {
		parent::setUp();
		$this->l10n = $this->createMock(IL10N::class);
		$this->l10n->method('t')->willReturnArgument(0);
		$this->urlGenerator = $this->createMock(IURLGenerator::class);
		$this->section = new PersonalSection($this->l10n, $this->urlGenerator);
	}

	public function testGetId(): void {
		$this->assertSame('doom_nextcloud', $this->section->getID());
	}

	public function testGetName(): void {
		$this->assertSame('Doom', $this->section->getName());
	}

	public function testGetPriorityIsInt(): void {
		$this->assertIsInt($this->section->getPriority());
	}

	public function testGetIconUsesAppIcon(): void {
		$this->urlGenerator->method('imagePath')
			->with('doom_nextcloud', 'app-dark.svg')
			->willReturn('/apps/doom_nextcloud/img/app-dark.svg');

		$this->assertSame('/apps/doom_nextcloud/img/app-dark.svg', $this->section->getIcon());
	}
}
