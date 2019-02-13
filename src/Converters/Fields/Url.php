<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Url extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $url = $this->sanitize($field->toString());

        $this->root()->nodeValue = $url;
        $this->addAttributes(parse_url($url));
    }
}
