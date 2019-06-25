<?php

use Hananils\Cache;

return function ($status, $user) {
    Cache::clearAssociations($user);
};
