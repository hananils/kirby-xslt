<?php

namespace Hananils\Converters\Fields;

use Hananils\Converters\File;
use Hananils\Xml;

class Files extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        foreach ($field->toFiles() as $child) {
            $user = new File('file');
            $user->import($child);

            $this->addElement('file', $user->root());
        }
    }
}
