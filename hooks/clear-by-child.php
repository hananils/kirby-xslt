<?php

use Hananils\Cache;

return function ($page) {
    Cache::clear($page->parent());
};
