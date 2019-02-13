<?php

namespace Hananils\Converters\Fields;

use Hananils\Converters\Content;
use Hananils\Xml;

class Structure extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        foreach ($field->toStructure() as $item) {
            $content = new Content('item');
            $content->setIncluded($this->included);
            $content->parse($item->content(), $blueprint['fields']);

            $this->addElement('item', $content->root());
        }
    }
}
