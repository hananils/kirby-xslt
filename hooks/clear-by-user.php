<?php

use Hananils\CacheAssociative;

return function ($newUser, $oldUser) {
    CacheAssociative::clearAssociations($oldUser);
};
