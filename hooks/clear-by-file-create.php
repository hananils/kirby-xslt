<?php

use Hananils\CacheAssociative;

return function ($file) {
    CacheAssociative::clear($file->page());
};
