<?php

namespace Hananils\Converters;

use Hananils\CacheAssociative;
use Hananils\Converters\Content;
use Hananils\Converters\Utilities\Path;
use Hananils\Xml;

class Page extends Xml
{
    public $included = [
        'attributes' => ['id', 'url'],
        'title' => true,
        'path' => false,
        'languages' => false,
        'content' => false,
        'files' => false
    ];

    public $includedTrue = [
        'attributes' => ['id', 'num', 'parent', 'slug', 'status', 'intended-template', 'uid', 'url'],
        'title' => true,
        'path' => true,
        'languages' => true,
        'content' => true,
        'files' => true,
        'children' => [
            'title' => true
        ]
    ];

    public function import($page)
    {
        if ($this->caching && $cache = CacheAssociative::get($page, $this->name, $this->included)) {
            $this->root = $cache;
        } else {
            $this->addNodeAttributes($page);

            $this->addNode('title', $page);
            $this->addNode('path', $page);
            $this->addNode('languages', $page);
            $this->addNode('content', $page);
            $this->addNode('children', $page);
            $this->addNode('files', $page);

            if ($this->caching) {
                CacheAssociative::set($page, $this->generate(), $this->name, $this->included);
            }
        }
    }

    public function addTitle($page)
    {
        $title = htmlspecialchars($page->title()->toString());
        $this->addElement('title', $title);
    }

    public function addPath($page)
    {
        $path = new Path('path');
        $path->parse($page);

        $this->addElement('path', $path->root());
    }

    public function addLanguages($page)
    {
        $languages = $this->addElement('languages');

        foreach (kirby()->languages() as $language) {
            $item = $this->addElement('language', $language->name(), [
                'code' => $language->code(),
                'url' => $page->urlForLanguage($language->code())
            ], $languages);
        }
    }

    public function addContent($page)
    {
        $content = new Content('content');
        $content->setIncluded($this->included['content']);
        $content->parse($page->content(), $page->blueprint()->fields(), $page);

        $this->addElement('content', $content->root());
    }

    public function addChildren($page)
    {
        if ($page->hasChildren()) {
            $children = new Pages('children');
            $children->setIncluded($this->included['children']);

            if (isset($this->included['children']['drafts']) && $this->included['children']['drafts'] === true) {
                $children->import($page->childrenAndDrafts());
            } else {
                $children->import($page->children());
            }

            $this->addElement('children', $children->root());
        }
    }

    public function addFiles($page)
    {
        if ($page->hasFiles()) {
            $files = new Files('files');
            $files->setIncluded($this->included['files']);
            $files->import($page->files());

            $this->addElement('files', $files->root());
        }
    }
}
