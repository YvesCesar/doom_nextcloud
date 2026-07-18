(function () {
    'use strict'

    var APP_ID = 'doom_nextcloud'
    var BANNER_DISMISS_KEY = APP_ID + '.keyBannerDismissed'

    var banner = document.getElementById('doom-key-banner')
    if (banner) {
        var settingsLink = document.getElementById('doom-key-banner-settings')
        if (settingsLink) {
            settingsLink.href = OC.generateUrl('/settings/user/' + APP_ID)
        }
        var dismissed = false
        try {
            dismissed = localStorage.getItem(BANNER_DISMISS_KEY) === '1'
        } catch (e) {
        }
        if (!dismissed) {
            banner.style.display = 'flex'
        }
        var dismissButton = document.getElementById('doom-key-banner-dismiss')
        if (dismissButton) {
            dismissButton.addEventListener('click', function () {
                try {
                    localStorage.setItem(BANNER_DISMISS_KEY, '1')
                } catch (e) {
                }
                banner.style.display = 'none'
            })
        }
    }

    // The account was already injected into localStorage by inject-account.js
    // (before js-dos loaded), so we only need to boot the emulator here.
    Dos(document.getElementById('doom'), {
        url: OC.generateUrl('/apps/' + APP_ID + '/bundle/doom.jsdos'),
        pathPrefix: (OC.webroot || '') + '/index.php/apps/' + APP_ID + '/emulators/',
    })
})()
