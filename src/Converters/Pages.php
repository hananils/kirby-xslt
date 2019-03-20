<?php

namespace Hananils\Converters;

use Hananils\Converters\Page;
use Hananils\Xml;

class Pages extends Xml
{
    public $included = [
        'title' => true,
        'path' => false,
        'languages' => false,
        'content' => false,
        'files' => false,
        'children' => false
    ];

    public $includedTrue = [
        'title' => true
    ];

    public function import($pages)
    {
        if ($pages->isEmpty()) {
            return;
        }

        $context = $pages->pagination();
        if ($context && $context->hasPages()) {
            $pagination = $this->addElement('pagination', null, [
                'limit' => $context->limit(),
                'offset' => $context->offset(),
                'page' => $context->page(),
                'pages' => $context->pages(),
                'total' => $context->total()
            ]);

            $this->addElement('first', $context->firstPageUrl(), null, $pagination);
            $this->addElement('previous', $context->prevPageUrl(), null, $pagination);
            $this->addElement('current', $context->pageUrl(), null, $pagination);
            $this->addElement('next', $context->nextPageUrl(), null, $pagination);
            $this->addElement('last', $context->lastPageUrl(), null, $pagination);
        }

        foreach ($pages->data() as $child) {
            $page = new Page('page');

            if (is_array($this->included) && array_key_exists('children', $this->included) && count($this->included) === 1) {
                $page->setIncluded($this->included['children']);
            } else {
                $page->setIncluded($this->included);
            }

            $page->import($child);

            $this->addElement('page', $page->root());
        }
    }
}
