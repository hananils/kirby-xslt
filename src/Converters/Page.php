<?php

namespace Hananils\Converters;

use Hananils\Cache;
use Hananils\Converters\Content;
use Hananils\Converters\Utilities\Path;
use Hananils\Xml;

class Page extends Xml
{
    public $name;

    public $included = [
        'title' => true,
        'path' => false,
        'languages' => false,
        'content' => false,
        'files' => false
    ];

    public $includedTrue = [
        'title' => true,
        'path' => true,
        'languages' => false,
        'content' => true,
        'files' => true,
        'children' => [
            'title' => true
        ]
    ];

    public function __construct($root = 'page', $version = '1.0', $encoding = 'utf-8')
    {
        parent::__construct($root, $version, $encoding);

        $this->name = $root;

        if (kirby()->languages()->isNotEmpty()) {
            $this->included['language'] = true;
        }
    }

    public function import($page)
    {
        if ($cache = Cache::get($page, $this->name, $this->included)) {
            $this->root = $cache;
        } else {
            $this->addAttributes([
                'id' => $page->id(),
                'num' => $page->num(),
                'parent' => $page->parent(),
                'slug' => $page->slug(),
                'status' => $page->status(),
                'template' => $page->intendedTemplate(),
                'uid' => $page->uid(),
                'url' => $page->url()
            ]);

            $this->addNode('title', $page);
            $this->addNode('path', $page);
            $this->addNode('languages', $page);
            $this->addNode('content', $page);
            $this->addNode('children', $page);
            $this->addNode('files', $page);

            if (option('hananils.xslt.cache') === true) {
                Cache::set($page, $this->name, $this->included, $this->generate());
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
            $item = $this->addElement('languages', $language->name(), [
                'code' => $language->code(),
                'url' => $page->urlForLanguage($language->code())
            ], $languages);
        }
    }

    public function addContent($page)
    {
        $content = new Content('content');
        $content->setIncluded($this->included['content']);
        $content->parse($page->content(), $page->blueprint()->fields(), $page->errors());

        $this->addElement('content', $content->root());
    }

    public function addChildren($page)
    {
        if ($page->hasChildren()) {
            $children = new Pages('children');
            $children->setIncluded($this->included['children']);
            $children->import($page->children());

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
