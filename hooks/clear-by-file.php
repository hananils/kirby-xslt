<?php

use Hananils\Cache;

return function ($newFile, $oldFile) {
    Cache::clear($oldFile->page());
};
