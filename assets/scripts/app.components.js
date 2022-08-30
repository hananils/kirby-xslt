/**
 * Component Elements
 *
 * hana+nils · Büro für Gestaltung
 * http://hananils.de · buero@hananils.de
 */

var App = App || {};

(function () {
    'use strict';

    /**
     * Element cache
     */
    var cache = {};

    /**
     * Set element.
     *
     * @param string id
     *  Element id
     * @param Element element
     *  Element node
     */
    var set = function (name, element) {
        if (!element) {
            element = document.getElementById(name);
        }

        cache[name] = element;
        return element;
    };

    /**
     * Get elements. Returns known elements from cache and
     * queries and caches unkown elements.
     *
     * @param string id
     *  The element id
     */
    var get = function (name) {
        if (!cache[name]) {
            set(name);
        }

        return cache[name];
    };

    /*--------------------------------------------------------------------------
     * Public
     *------------------------------------------------------------------------*/

    App.Components = {
        set: set,
        get: get
    };
})();
