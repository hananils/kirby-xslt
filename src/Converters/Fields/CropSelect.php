<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class CropSelect extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $this->root->nodeValue = $this->sanitize($field->toString());
    }
}
