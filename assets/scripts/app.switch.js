/**
 * hana+nils · Büro für Gestaltung
 * https://hananils.de · buero@hananils.de
 */

var App = App || {};

(function() {
    'use strict';

    var init = function() {
        App.Components.get('xml').addEventListener('click', handleHighlight);

        var switches = document.querySelectorAll('.m-index-switch');
        switches.forEach(function(status) {
            status.addEventListener('click', handleClick);
        });

        restore();
    };

    /*--------------------------------------------------------------------------
     * Events
     *------------------------------------------------------------------------*/

    var handleClick = function(event) {
        var target = event.target;

        if (target.matches('use')) {
            target = target.parentNode.parentNode.parentNode;
        }

        var name = target.previousSibling.href.split('#').pop();

        toggle(name);
    };

    var handleHighlight = function(event) {
        var target = event.target;

        if (target.parentNode.parentNode.matches('.is-collapsed')) {
            var name = target.href.split('#').pop();

            toggle(name);
        }
    };

    /*--------------------------------------------------------------------------
     * Interactions
     *------------------------------------------------------------------------*/

    var toggle = function(name) {
        var button = App.Components.get('index').querySelector(
            'a[href$="#' + name + '"] + .m-index-switch'
        );
        var node = App.Components.get('app').querySelector(
            'a[id="' + name + '"] + .node'
        );

        if (button.parentNode.classList.contains('is-collapsed')) {
            open(button, node, name);
        } else {
            close(button, node, name);
        }
    };

    var open = function(button, node, name) {
        button.parentNode.classList.remove('is-collapsed');
        node.classList.remove('is-collapsed');

        setIcon(button, 'off', 'on');
        forget(name);
    };

    var close = function(button, node, name) {
        button.parentNode.classList.add('is-collapsed');
        node.classList.add('is-collapsed');

        setIcon(button, 'on', 'off');
        store(name);
    };

    var setIcon = function(button, from, to) {
        var use = button.querySelector('use');
        var icon = use.getAttribute('xlink:href');

        use.setAttribute(
            'xlink:href',
            icon.replace('icon-toggle-' + from, 'icon-toggle-' + to)
        );
    };

    var store = function(name) {
        var storage = localStorage.getItem('kirby-xslt.collapsed') || '';
        var collapsed = storage.split(',');

        collapsed.push(name);
        collapsed = collapsed.filter(clean);

        localStorage.setItem('kirby-xslt.collapsed', collapsed.join(','));
    };

    var forget = function(name) {
        var storage = localStorage.getItem('kirby-xslt.collapsed') || '';
        var collapsed = storage.split(',').filter(clean);

        if (collapsed.indexOf(name) > -1) {
            collapsed.splice(collapsed.indexOf(name), 1);
        }

        localStorage.setItem('kirby-xslt.collapsed', collapsed.join(','));
    };

    var restore = function() {
        var storage = localStorage.getItem('kirby-xslt.collapsed');

        if (!storage) {
            return;
        }

        var collapsed = storage.split(',').filter(clean);

        collapsed.forEach(function(name) {
            toggle(name);
        });
    };

    /*--------------------------------------------------------------------------
     * Utilities
     *------------------------------------------------------------------------*/

    var clean = function(value, index, self) {
        if (!value) {
            return false;
        }

        return self.indexOf(value) === index;
    };

    /*--------------------------------------------------------------------------
     * Public
     *------------------------------------------------------------------------*/

    App.Register.add('Switch', '.m-index-switch');

    App.Switch = {
        init: init
    };
})();
