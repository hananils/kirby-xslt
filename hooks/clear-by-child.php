<?php

use Hananils\CacheAssociative;

return function ($page) {
    CacheAssociative::clear($page->parent());
};
