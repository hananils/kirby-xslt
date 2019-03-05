<?php

use Hananils\Cache;

return function ($newUser, $oldUser) {
    Cache::clearAssociations($oldUser);
};
