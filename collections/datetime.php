<?php

use Hananils\Converters\Dates;

return function ($kirby) {
    $datetime = new Dates('datetime');
    $datetime->parse('now');

    return $datetime->document();
};
