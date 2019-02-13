/**
 * PLANT Architect Inc.
 *
 * hana+nils · Büro für Gestaltung
 * https://hananils.de · buero@hananils.de
 */

var App = App || {};

(function() {
    'use strict';

    var init = function() {
        App.Components.get('app').classList.add('is-interactive');

        // Init modules
        var modules = App.Register.get();
        for (var i = 0; i < modules.length; i++) {
            if (document.querySelector(modules[i].trigger)) {
                App[modules[i].name].init();
            }
        }
    };

    var error = function() {
        document.body.className = '';
    };

    /*--------------------------------------------------------------------------
      * Public
      *------------------------------------------------------------------------*/

    App.init = init;
    App.error = error;
})();

/*-----------------------------------------------------------------------------
 	Initialise Site
 -----------------------------------------------------------------------------*/

document.addEventListener('DOMContentLoaded', App.init);
window.addEventListener('error', App.error);
