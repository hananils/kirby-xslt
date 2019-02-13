<?php

namespace Hananils\Converters;

use Hananils\Converters\Content;
use Hananils\Converters\Utilities\Path;
use Hananils\Xml;

class Page extends Xml {
    public $included = [
        'title' => true,
        'path' => false,
        'content' => false,
        'files' => false
    ];

    public function import($page) {
        $this->addAttributes([
            'id' => $page->id(),
            'num' => $page->num(),
            'parent' => $page->parent(),
            'slug' => $page->slug(),
            'status' => $page->status(),
            'template' => $page->template(),
            'uid' => $page->uid(),
            'url' => $page->url()
        ]);

        $this->addNode('title', $page);
        $this->addNode('path', $page);
        $this->addNode('content', $page);
        $this->addNode('children', $page);
        $this->addNode('files', $page);
    }

    public function addTitle($page) {
        $title = htmlspecialchars($page->title()->toString());
        $this->addElement('title', $title);
    }

    public function addPath($page) {
        $path = new Path('path');
        $path->parse($page);

        $this->addElement('path', $path->root());
    }

    public function addContent($page) {
        $content = new Content('content');
        $content->setIncluded($this->included['content']);
        $content->parse($page->content(), $page->blueprint()->fields());

        $this->addElement('content', $content->root());
    }

    public function addChildren($page) {
        if ($page->hasChildren()) {
            $children = new Pages('children');
            $children->setIncluded($this->included['children']);
            $children->import($page->children());

            $this->addElement('children', $children->root());
        }
    }

    public function addFiles($page) {
        if ($page->hasFiles()) {
            $files = new Files('files');
            $files->setIncluded($this->included['files']);
            $files->import($page->files());

            $this->addElement('files', $files->root());
        }
    }
}
