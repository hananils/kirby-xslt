<?php

use Hananils\CacheAssociative;

return function ($page, $force) {
    CacheAssociative::clear($page);
    CacheAssociative::clear($page->parent());
};
