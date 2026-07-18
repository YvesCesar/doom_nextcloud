<?php

declare(strict_types=1);

use OCA\Doom\AppInfo\Application;
use OCP\Util;

Util::addScript(Application::APP_ID, 'personal-settings');
Util::addStyle(Application::APP_ID, 'personal-settings');

/** @var \OCP\IL10N $l */
/** @var array{email: ?string} $_ */
$email = $_['email'] ?? null;
$signedIn = is_string($email) && $email !== '';
?>

<div id="doom-personal-settings" class="section">
	<h2><?php p($l->t('Doom')); ?></h2>
	<p class="settings-hint">
		<?php p($l->t('Enter your js-dos key to stay signed in across browsers and devices. It is validated with js-dos and stored encrypted for your account.')); ?>
	</p>

	<div id="doom-key-signed-in" data-email="<?php p($email ?? ''); ?>"
		 style="<?php p($signedIn ? '' : 'display: none;'); ?>">
		<p class="doom-key-identity">
			<span aria-hidden="true" class="doom-key-check">&#10003;</span>
			<span>
				<?php p($l->t('Signed in as')); ?>
				<strong id="doom-key-email"><?php p($email ?? ''); ?></strong>
			</span>
		</p>
		<div class="doom-key-actions">
			<button id="doom-key-change" type="button"><?php p($l->t('Use a different key')); ?></button>
			<button id="doom-key-forget" type="button"><?php p($l->t('Remove key')); ?></button>
		</div>
	</div>

	<div id="doom-key-form" style="<?php p($signedIn ? 'display: none;' : ''); ?>">
		<div class="doom-key-row">
			<input id="doom-key-input" type="text" maxlength="5" autocomplete="off"
				   autocapitalize="off" spellcheck="false" placeholder="abcde">
			<button id="doom-key-save" class="primary" type="button"><?php p($l->t('Save')); ?></button>
			<button id="doom-key-cancel" type="button" style="display: none;"><?php p($l->t('Cancel')); ?></button>
		</div>
	</div>

	<p id="doom-key-status" aria-live="polite"></p>
</div>
