<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Text extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $field = $this->applyMethods($field, $this->included);

        $this->addAttribute('slug', $field->slug());
        $this->root->nodeValue = $this->sanitize($field->toString());
    }
}
