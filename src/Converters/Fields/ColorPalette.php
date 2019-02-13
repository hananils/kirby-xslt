<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class ColorPalette extends Xml {
    public function parse($field, $blueprint) {
        if ($field->isEmpty()) {
            return;
        }

        $value = $field->value();

        if (is_array($value)) {
            foreach ($value as $name => $color) {
                if (is_numeric($name)) {
                    if ($name === 0) {
                        $this->addElement('selected', $color);
                    } else {
                        $extracted = $this->addElement('selected');
                        foreach ($color as $extract) {
                            $this->addElement('color', $extract, null, $extracted);
                        }
                    }
                } else {
                    $this->addElement($name, $color);
                }
            }
        } else {
            $this->root->nodeValue = $this->sanitize($value);
        }
    }
}
