(function () {
    'use strict'

    var APP_ID = 'doom_nextcloud'
    var KEY_URL = OC.generateUrl('/apps/' + APP_ID + '/jsdos-key')
    var STATE_URL = OC.generateUrl('/apps/' + APP_ID + '/jsdos-state')

    function el(id) {
        return document.getElementById(id)
    }

    function headers(extra) {
        var base = { requesttoken: OC.requestToken, Accept: 'application/json' }
        if (extra) {
            Object.keys(extra).forEach(function (key) {
                base[key] = extra[key]
            })
        }
        return base
    }

    function setStatus(message, ok) {
        var status = el('doom-key-status')
        if (status) {
            status.textContent = message
            status.style.color = ok ? 'var(--color-success)' : 'var(--color-error)'
        }
    }

    function signedIn(email) {
        setStatus(t(APP_ID, 'Signed in as {email}', { email: email }), true)
    }

    function save() {
        var input = el('doom-key-input')
        var key = (input.value || '').trim()
        if (!key) {
            return
        }
        setStatus(t(APP_ID, 'Validating…'), true)
        fetch(KEY_URL, {
            method: 'PUT',
            headers: headers({ 'Content-Type': 'application/json' }),
            body: JSON.stringify({ key: key }),
        }).then(function (response) {
            if (response.ok) {
                return response.json().then(function (data) {
                    input.value = ''
                    signedIn(data.account && data.account.email)
                })
            }
            if (response.status === 422) {
                setStatus(t(APP_ID, 'Invalid key.'), false)
            } else {
                setStatus(t(APP_ID, 'Could not save the key.'), false)
            }
        }).catch(function () {
            setStatus(t(APP_ID, 'Could not save the key.'), false)
        })
    }

    function forget() {
        fetch(STATE_URL, { method: 'DELETE', headers: headers() })
            .then(function (response) {
                setStatus(
                    response.ok ? t(APP_ID, 'Key removed.') : t(APP_ID, 'Could not remove the key.'),
                    response.ok
                )
            }).catch(function () {
                setStatus(t(APP_ID, 'Could not remove the key.'), false)
            })
    }

    function init() {
        var saveButton = el('doom-key-save')
        var forgetButton = el('doom-key-forget')
        var input = el('doom-key-input')
        if (saveButton) {
            saveButton.addEventListener('click', save)
        }
        if (forgetButton) {
            forgetButton.addEventListener('click', forget)
        }
        if (input) {
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    save()
                }
            })
        }
        var status = el('doom-key-status')
        var email = status && status.getAttribute('data-email')
        if (email) {
            signedIn(email)
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init)
    } else {
        init()
    }
})()
