<?php

use Hananils\Converters\Dates;
use Hananils\Definitions\Definitions;

return function ($kirby) {
    $template = page()->template();
    $definitions = new Definitions($template);
    $included = $definitions->get('datetime');

    $datetime = new Dates('datetime');
    $datetime->setIncluded($included);
    $datetime->parse('now');

    return $datetime->document();
};
