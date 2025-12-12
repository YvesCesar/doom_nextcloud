<?php

declare(strict_types=1);

use OCP\Util;

Util::addScript(OCA\Doom\AppInfo\Application::APP_ID, 'js-dos');
Util::addStyle(OCA\Doom\AppInfo\Application::APP_ID, 'js-dos');
Util::addScript(OCA\Doom\AppInfo\Application::APP_ID, 'load-game');

?>

<div id="doom" style="width: 100%;"></div>
