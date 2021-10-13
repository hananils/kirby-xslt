<?php

use Hananils\CacheAssociative;

return function ($status, $file) {
    CacheAssociative::clear($file->page());
};
