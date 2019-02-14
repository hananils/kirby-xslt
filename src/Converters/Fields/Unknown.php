<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Unknown extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $this->addAttribute('error', 'type unknown');
    }
}
