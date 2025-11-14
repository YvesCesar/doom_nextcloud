<?php

declare(strict_types=1);

use OCP\Util;

Util::addScript(OCA\Doom\AppInfo\Application::APP_ID, OCA\Doom\AppInfo\Application::APP_ID . '-main');
Util::addStyle(OCA\Doom\AppInfo\Application::APP_ID, OCA\Doom\AppInfo\Application::APP_ID . '-main');

?>

<div id="doom"></div>
