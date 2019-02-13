<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Toggle extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $toggled = $field->toBool();

        $this->addAttribute('toggled', $toggled);
        $this->root->nodeValue = $toggled === true ? 'yes' : 'no';
    }
}
