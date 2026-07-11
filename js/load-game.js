(function () {
    'use strict'

    var APP_ID = 'doom_nextcloud'

    // The account was already injected into localStorage by inject-account.js
    // (before js-dos loaded), so we only need to boot the emulator here.
    Dos(document.getElementById('doom'), {
        url: OC.generateUrl('/apps/' + APP_ID + '/bundle/doom.jsdos'),
        pathPrefix: (OC.webroot || '') + '/index.php/apps/' + APP_ID + '/emulators/',
    })
})()
