<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Cache;
use Hananils\Xslt;
use Kirby\Cms\App;

Kirby::plugin('hananils/kirby-xslt', [
    'options' => [
        'cache' => true
    ],
    'components' => [
        'template' => function (
            App $kirby,
            string $name,
            string $type = null,
            string $defaultType = null
        ) {
            if (
                ($type === 'html' && strpos($name, 'emails/') !== 0) ||
                in_array($type, option('hananils.kirby-xslt.types', ['xml']))
            ) {
                return new Hananils\Xslt($name, $type);
            }

            // default to native component
            return $kirby->nativeComponent('template')(
                $kirby,
                $name,
                $type,
                $defaultType
            );
        }
    ],
    'collections' => [
        'assets' => require __DIR__ . '/collections/assets.php',
        'datetime' => require __DIR__ . '/collections/datetime.php'
    ],
    'hooks' => [
        'site.update:after' => require __DIR__ . '/hooks/clear-by-page.php',
        'page.changeNum:after' => require __DIR__ . '/hooks/clear-by-page.php',
        'page.changeSlug:after' => require __DIR__ . '/hooks/clear-by-page.php',
        'page.changeStatus:after' => require __DIR__ .
            '/hooks/clear-by-page.php',
        'page.changeTemplate:after' => require __DIR__ .
            '/hooks/clear-by-page.php',
        'page.changeTitle:after' => require __DIR__ .
            '/hooks/clear-by-page.php',
        'page.update:after' => require __DIR__ . '/hooks/clear-by-page.php',
        'page.create:after' => require __DIR__ . '/hooks/clear-by-child.php',
        'page.delete:after' => require __DIR__ . '/hooks/clear-delete.php',
        'file.changeName:after' => require __DIR__ . '/hooks/clear-by-file.php',
        'file.changeSort:after' => require __DIR__ . '/hooks/clear-by-file.php',
        'file.replace:after' => require __DIR__ . '/hooks/clear-by-file.php',
        'file.update:after' => require __DIR__ . '/hooks/clear-by-file.php',
        'file.create:after' => require __DIR__ .
            '/hooks/clear-by-file-create.php',
        'file.delete:after' => require __DIR__ .
            '/hooks/clear-by-file-delete.php',
        'user.changeEmail:after' => require __DIR__ .
            '/hooks/clear-by-user.php',
        'user.changeName:after' => require __DIR__ . '/hooks/clear-by-user.php',
        'user.changeLanguage:after' => require __DIR__ .
            '/hooks/clear-by-user.php',
        'user.changeRole:after' => require __DIR__ . '/hooks/clear-by-user.php',
        'user.update:after' => require __DIR__ . '/hooks/clear-by-user.php',
        'user.delete:after' => require __DIR__ .
            '/hooks/clear-by-user-delete.php'
    ],
    'routes' => [
        [
            'pattern' => '(:all)',
            'action' => function ($path) {
                if (get('data') === 'clear') {
                    Cache::clear();
                    go($path . '?data');
                }

                $this->next();
            }
        ]
    ],
    'translations' => [
        'en' => [
            'cache-off' => 'XML cache is off',
            'cache-on' => 'XML cache is on',
            'cache-switch' => 'You can change this in your configuration file.',
            'data' => 'Data',
            'error' => 'Processing Error',
            'errors' => 'Processing Errors',
            'execution' => 'XML generation',
            'open' => 'Open',
            'overview' => 'Overview',
            'panel' => 'Panel',
            'rendering' => 'Approximate rendering time',
            'result' => 'Result',
            'search' => 'Search',
            'stats' => 'Statistic',
            'transformation' => 'XSLT transformation',
            'xpath' => 'find by XPath …'
        ],
        'de' => [
            'cache-off' => 'XML-Cache ist deaktiviert',
            'cache-on' => 'XML-Cache ist aktiviert',
            'cache-switch' =>
                'Du kannst diese Einstellung in deiner Konfiguration ändern.',
            'data' => 'Daten',
            'error' => 'Verarbeitungsfehler',
            'errors' => 'Verarbeitungsfehler',
            'execution' => 'XML-Generierung',
            'open' => 'Öffnen',
            'overview' => 'Übersicht',
            'panel' => 'Panel',
            'rendering' => 'Ungefähre Ausführungszeit',
            'result' => 'Ergebnis',
            'search' => 'Suche',
            'stats' => 'Statistik',
            'transformation' => 'XSLT-Transformation',
            'xpath' => 'mit XPath finden …'
        ]
    ]
]);
