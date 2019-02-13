<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Time extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        list($hours, $minutes) = explode(':', $field->toString());

        $this->setAttributes([
            'hours' => $hours,
            'minutes' => $minutes
        ]);
        $this->root->nodeValue = $field->toString();
    }
}
