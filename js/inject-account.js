(function () {
    'use strict'

    // Runs BEFORE js-dos.js: js-dos reads its account from this prefixed
    // localStorage key at script-eval time, so the account (provided by the
    // server as initial state) must already be there when js-dos loads.
    try {
        var account = OCP.InitialState.loadState('doom_nextcloud', 'account')
        if (account && account.email) {
            localStorage.setItem('jsdos.8.cached.jsdos.account', JSON.stringify(account))
        }
    } catch (e) {
        // No account or initial state: js-dos simply starts logged out.
    }
})()
