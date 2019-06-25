<?php

namespace Hananils\Converters\Fields;

use Hananils\CacheAssociative;
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

            if ($this->included !== true) {
                $page->setIncluded($this->included);
            }

            $page->import($child);

            $this->addElement('page', $page->root());

            CacheAssociative::setAssociation($child, $field->parent());
        }
    }
}
