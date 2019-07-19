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

        if (isset($blueprint['contrast'])) {
            if (is_array($blueprint['contrast'])) {
                $readable = $field->toMostReadable($blueprint['contrast']);
            } else {
                $readable = $field->toMostReadable(['#fff', '#000']);
            }

            $this->addAttribute('most-readable', $readable);
        }

        unset($attributes['original']);

        $this->addAttributes($attributes);
        $this->root->nodeValue = $this->sanitize($value);
    }

}
