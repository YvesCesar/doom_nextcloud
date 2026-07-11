<?php

declare(strict_types=1);

use OCA\Doom\AppInfo\Application;
use OCP\Util;

Util::addScript(Application::APP_ID, 'personal-settings');

/** @var \OCP\IL10N $l */
/** @var array{email: ?string} $_ */
$email = $_['email'] ?? null;
?>

<div id="doom-personal-settings" class="section">
	<h2><?php p($l->t('Doom')); ?></h2>
	<p class="settings-hint">
		<?php p($l->t('Enter your js-dos key to stay signed in across browsers and devices. It is validated with js-dos and stored encrypted for your account.')); ?>
	</p>
	<div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
		<input id="doom-key-input" type="text" maxlength="5" autocomplete="off"
			   autocapitalize="off" spellcheck="false" placeholder="abcde">
		<button id="doom-key-save" class="primary" type="button"><?php p($l->t('Save')); ?></button>
		<button id="doom-key-forget" type="button"><?php p($l->t('Remove')); ?></button>
	</div>
	<p id="doom-key-status" aria-live="polite" data-email="<?php p($email ?? ''); ?>"></p>
</div>
