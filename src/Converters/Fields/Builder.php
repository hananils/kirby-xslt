<?php

namespace Hananils\Converters\Fields;

use Hananils\Converters\Content;
use Hananils\Xml;

class Builder extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        foreach ($field->toBuilderBlocks() as $block) {
            $key = $block->_key()->toString();
            $fields = $blueprint['fieldsets'][$key]['fields'];

            $content = new Content($key);
            $content->setIncluded($this->included);
            $content->parse($block->content(), $fields);

            $this->addElement($key, $content->root());
        }
    }
}
