<?php

declare(strict_types=1);

use OCP\Util;

Util::addScript(OCA\Doom\AppInfo\Application::APP_ID, 'js-dos');
Util::addStyle(OCA\Doom\AppInfo\Application::APP_ID, 'js-dos');

?>

<div id="doom"></div>
