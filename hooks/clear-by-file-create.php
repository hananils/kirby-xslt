<?php

use Hananils\Cache;

return function ($file) {
    Cache::clear($file->page());
};
