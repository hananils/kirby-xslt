<?php

use Hananils\CacheAssociative;

return function ($newFile, $oldFile) {
    CacheAssociative::clear($oldFile->page());
};
