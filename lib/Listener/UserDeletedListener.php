<?php

declare(strict_types=1);

namespace OCA\Doom\Listener;

use OCA\Doom\Service\JsDosAccountService;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\User\Events\UserDeletedEvent;

/**
 * @template-implements IEventListener<UserDeletedEvent>
 */
class UserDeletedListener implements IEventListener {
	public function __construct(
		private JsDosAccountService $accountService,
	) {
	}

	public function handle(Event $event): void {
		if (!$event instanceof UserDeletedEvent) {
			return;
		}

		$this->accountService->delete($event->getUser()->getUID());
	}
}
