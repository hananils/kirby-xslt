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

        $attributes = $field->toValues();
        $value = $attributes['original'];

        if (isset($this->included['space'])) {
            $value = $field->toColor($this->included['space']);
        }

        unset($attributes['original']);

        $this->addAttributes($attributes);
        $this->root->nodeValue = $this->sanitize($value);
    }

}
