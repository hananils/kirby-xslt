<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Colors extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $this->addAttribute('readable', $field->toMostReadable());
        $this->root->nodeValue = $this->sanitize($field->toColor());
    }
}
