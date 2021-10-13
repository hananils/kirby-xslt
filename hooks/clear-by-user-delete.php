<?php

use Hananils\CacheAssociative;

return function ($status, $user) {
    CacheAssociative::clearAssociations($user);
};
