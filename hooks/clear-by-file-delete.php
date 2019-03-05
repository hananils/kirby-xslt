<?php

use Hananils\Cache;

return function ($status, $file) {
    Cache::clear($file->page());
};
