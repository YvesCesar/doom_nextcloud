<?php

/**
 * SPDX-FileCopyrightText: 2026 Yves César Amorim de Azevedo
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

use OCP\App\IAppManager;
use OCP\Server;

if (!defined('PHPUNIT_RUN')) {
	define('PHPUNIT_RUN', 1);
}

require_once __DIR__ . '/../../../../lib/base.php';
require_once __DIR__ . '/../../../../tests/autoload.php';

\OC::$composerAutoloader->addPsr4('Test\\', OC::$SERVERROOT . '/tests/lib/', true);
\OC::$composerAutoloader->addPsr4('OCA\\Doom\\Tests\\', __DIR__, true);

Server::get(IAppManager::class)->loadApp('doom_nextcloud');

OC_Hook::clear();
