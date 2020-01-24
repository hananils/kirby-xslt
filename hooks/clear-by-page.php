<?php

use Hananils\CacheAssociative;

return function ($newPage, $oldPage) {
    CacheAssociative::clear($oldPage);
};
