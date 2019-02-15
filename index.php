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
    ]
]);
