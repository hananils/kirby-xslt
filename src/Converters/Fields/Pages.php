<?php

namespace Hananils\Converters\Fields;

use Hananils\Cache;
use Hananils\Converters\Page;
use Hananils\Xml;

class Pages extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        foreach ($field->toPages() as $child) {
            $page = new Page('page');
            $page->import($child);

            $this->addElement('page', $page->root());

            Cache::setAssociation($child, $field->parent());
        }
    }
}
