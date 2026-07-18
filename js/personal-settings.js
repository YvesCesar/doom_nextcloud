(function () {
    'use strict'

    var APP_ID = 'doom_nextcloud'
    var KEY_URL = OC.generateUrl('/apps/' + APP_ID + '/jsdos-key')
    var STATE_URL = OC.generateUrl('/apps/' + APP_ID + '/jsdos-state')

    var currentEmail = null

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

    function showSignedIn(email) {
        currentEmail = email || currentEmail
        var emailEl = el('doom-key-email')
        if (emailEl) {
            emailEl.textContent = currentEmail || ''
        }
        el('doom-key-signed-in').style.display = ''
        el('doom-key-form').style.display = 'none'
        var input = el('doom-key-input')
        if (input) {
            input.value = ''
        }
    }

    function showForm() {
        el('doom-key-signed-in').style.display = 'none'
        el('doom-key-form').style.display = ''
        var cancel = el('doom-key-cancel')
        if (cancel) {
            cancel.style.display = currentEmail ? '' : 'none'
        }
        var input = el('doom-key-input')
        if (input) {
            input.focus()
        }
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
                    showSignedIn(data.account && data.account.email)
                    setStatus(t(APP_ID, 'Key validated and saved.'), true)
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
                if (response.ok) {
                    currentEmail = null
                    showForm()
                    setStatus(t(APP_ID, 'Key removed.'), true)
                } else {
                    setStatus(t(APP_ID, 'Could not remove the key.'), false)
                }
            }).catch(function () {
                setStatus(t(APP_ID, 'Could not remove the key.'), false)
            })
    }

    function init() {
        var signedInBlock = el('doom-key-signed-in')
        currentEmail = (signedInBlock && signedInBlock.getAttribute('data-email')) || null

        var saveButton = el('doom-key-save')
        var forgetButton = el('doom-key-forget')
        var changeButton = el('doom-key-change')
        var cancelButton = el('doom-key-cancel')
        var input = el('doom-key-input')
        if (saveButton) {
            saveButton.addEventListener('click', save)
        }
        if (forgetButton) {
            forgetButton.addEventListener('click', forget)
        }
        if (changeButton) {
            changeButton.addEventListener('click', function () {
                setStatus('', true)
                showForm()
            })
        }
        if (cancelButton) {
            cancelButton.addEventListener('click', function () {
                setStatus('', true)
                showSignedIn(currentEmail)
            })
        }
        if (input) {
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    save()
                }
            })
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init)
    } else {
        init()
    }
})()
