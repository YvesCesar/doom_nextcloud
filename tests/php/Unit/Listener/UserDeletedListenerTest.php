<?php

declare(strict_types=1);

namespace OCA\Doom\Tests\Unit\Listener;

use OCA\Doom\Listener\UserDeletedListener;
use OCA\Doom\Service\JsDosAccountService;
use OCP\EventDispatcher\Event;
use OCP\IUser;
use OCP\User\Events\UserDeletedEvent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserDeletedListenerTest extends TestCase {
	private JsDosAccountService&MockObject $service;
	private UserDeletedListener $listener;

	protected function setUp(): void {
		parent::setUp();
		$this->service = $this->createMock(JsDosAccountService::class);
		$this->listener = new UserDeletedListener($this->service);
	}

	public function testDeletesStateForDeletedUser(): void {
		$user = $this->createMock(IUser::class);
		$user->method('getUID')->willReturn('alice');
		$this->service->expects($this->once())
			->method('delete')
			->with('alice');

		$this->listener->handle(new UserDeletedEvent($user));
	}

	public function testIgnoresUnrelatedEvent(): void {
		$this->service->expects($this->never())->method('delete');

		$this->listener->handle(new Event());
	}
}
