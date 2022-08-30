<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Focus extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $this->addElement(
            'x',
            $field->model()->focusPercentageX(),
            [
                'value' => $field->model()->focusX()
            ],
            $this->root
        );
        $this->addElement(
            'y',
            $field->model()->focusPercentageY(),
            [
                'value' => $field->model()->focusY()
            ],
            $this->root
        );
    }
}
