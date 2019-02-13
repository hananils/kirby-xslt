<?php

use Hananils\Converters\Directory;

return function ($kirby) {
    $assets = new Directory('assets');
    $assets->read($kirby->root('assets'));

    return $assets->document();
};
