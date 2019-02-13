<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Range extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $this->addAttribute('min', $blueprint['min']);
        $this->addAttribute('max', $blueprint['max']);
        $this->addAttribute('step', $blueprint['step']);
        $this->root->nodeValue = $this->sanitize($field->toFloat());
    }
}
