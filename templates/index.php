<?php

declare(strict_types=1);

use OCP\Util;

Util::addScript(OCA\Doom\AppInfo\Application::APP_ID, 'inject-account');
Util::addScript(OCA\Doom\AppInfo\Application::APP_ID, 'js-dos');
Util::addStyle(OCA\Doom\AppInfo\Application::APP_ID, 'js-dos');
Util::addStyle(OCA\Doom\AppInfo\Application::APP_ID, 'doom');
Util::addScript(OCA\Doom\AppInfo\Application::APP_ID, 'load-game');

/** @var \OCP\IL10N $l */
/** @var array{email: ?string} $_ */
$email = $_['email'] ?? null;
$signedIn = is_string($email) && $email !== '';
?>

<div class="doom-wrapper">
	<div id="doom-key-banner" role="note">
		<span aria-hidden="true">&#128273;</span>
		<span class="doom-key-banner-text">
			<?php if ($signedIn): ?>
				<?php p($l->t('Signed in to js-dos as %s.', [$email])); ?>
				<a id="doom-key-banner-settings" href="#"><?php p($l->t('Manage key')); ?></a>
			<?php else: ?>
				<?php p($l->t('New: save your js-dos key once and stay signed in on all your devices.')); ?>
				<a id="doom-key-banner-settings" href="#"><?php p($l->t('Set it up')); ?></a>
			<?php endif; ?>
		</span>
		<button id="doom-key-banner-dismiss" type="button"
				title="<?php p($l->t('Dismiss')); ?>" aria-label="<?php p($l->t('Dismiss')); ?>">
			&times;
		</button>
	</div>

	<div id="doom"></div>
</div>
