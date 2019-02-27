<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Hananils\Xslt;
use Kirby\Cms\App;

Kirby::plugin('hananils/xslt', [
    'components' => [
        'template' => function (App $kirby, string $name, string $contentType = null) {
            return new Hananils\Xslt($name, $contentType);
        }
    ],
    'collections' => [
        'assets' => require 'collections/assets.php',
        'datetime' => require 'collections/datetime.php'
    ],
    'translations' => [
        'en' => [
            'data' => 'Data',
            'error' => 'Processing Error',
            'errors' => 'Processing Errors',
            'execution' => 'XML generation time',
            'open' => 'Open',
            'overview' => 'Overview',
            'panel' => 'Panel',
            'result' => 'Result',
            'search' => 'Search',
            'xpath' => 'find by XPath …'
        ],
        'de' => [
            'data' => 'Daten',
            'error' => 'Verarbeitungsfehler',
            'errors' => 'Verarbeitungsfehler',
            'execution' => 'XML-Generierungszeit',
            'open' => 'Öffnen',
            'overview' => 'Übersicht',
            'panel' => 'Panel',
            'result' => 'Ergebnis',
            'search' => 'Suche',
            'xpath' => 'mit XPath finden …'
        ]
    ]
]);
