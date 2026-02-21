Dos(document.getElementById("doom"), {
    url: OC.generateUrl('/apps/doom_nextcloud/bundle/doom'),
    pathPrefix: (OC.webroot || '') + '/index.php/apps/doom_nextcloud/emulators/',
});