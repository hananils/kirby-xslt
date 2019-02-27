/**
 * PLANT Architect Inc.
 *
 * hana+nils · Büro für Gestaltung
 * https://hananils.de · buero@hananils.de
 */

var App = App || {};

/**
 * Module register
 */

(function() {
    'use strict';

    var register = [];

    var add = function(name, trigger) {
        register.push({
            name: name,
            trigger: trigger
        });
    };

    var get = function() {
        return register;
    };

    /*--------------------------------------------------------------------------
     * Public
     *------------------------------------------------------------------------*/

    App.Register = {
        add: add,
        get: get
    };
})();
