<?php

use Hananils\CacheAssociative;

return function ($newSite, $oldSite) {
    CacheAssociative::clear($oldSite);
};
