<?php

namespace Hananils\Converters;

use Hananils\Converters\Page;
use Hananils\Xml;

class Pages extends Xml {
    public function import($pages) {
        if ($pages->isEmpty()) {
            return;
        }

        $context = $pages->pagination();
        if ($context) {
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

            if (is_array($this->included) && array_key_exists('children', $this->included)) {
                $page->setIncluded($this->included['children']);
            } else {
                $page->setIncluded($this->included);
            }

            $page->import($child);

            $this->addElement('page', $page->root());
        }
    }
}
