<?php

use Hananils\Cache;

return function ($newPage, $oldPage) {
    Cache::clear($oldPage);
};
