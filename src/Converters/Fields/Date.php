<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Date extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $this->addAttributes([
            'iso' => $field->toDate('c'),
            'year' => $field->toDate('Y'),
            'month' => $field->toDate('n'),
            'day' => $field->toDate('j'),
            'timestamp' => $field->toDate('U'),
            'time' => $field->toDate('H:i'),
            'weekday' => $field->toDate('N'),
            'offset' => $field->toDate('O')
        ]);
        $this->root->nodeValue = $field->toDate('Y-m-d');
    }
}
