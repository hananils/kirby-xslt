<?php

namespace Hananils\Converters;

use Hananils\Converters\User;
use Hananils\Converters\Utilities\Path;
use Hananils\Xml;
use Kirby\Toolkit\Str;

class Kirby extends Xml
{
    public function import($kirby)
    {
        $this->addAttributes([
            'content-extension' => $kirby->contentExtension(),
            'language' => $kirby->language(),
            'multilang' => $kirby->multilang(),
            'version' => $kirby->version()
        ]);

        $this->addNode('urls', $kirby);
        $this->addNode('request', $kirby);
        $this->addNode('user', $kirby);
    }

    public function addUrls($kirby)
    {
        $element = $this->addElement('urls');

        foreach ($kirby->urls()->toArray() as $name => $url) {
            $attributes = [];

            if ($url) {
                $attributes = parse_url($url);
            }

            $this->addElement($name, $url, $attributes, $element);
        }
    }

    public function addRequest($kirby)
    {
        $element = $this->addElement('request');

        $this->addPath($kirby, $element);
        $this->addParams($kirby, $element);
        $this->addQuery($kirby, $element);
    }

    public function addPath($kirby, $parent)
    {
        $url = $kirby->request()->url()->toString();
        $url = preg_replace('/\/(?!localhost)[-_0-9a-zA-Z]*\:.*/', '', $url);
        $url = preg_replace('/\/?\?.*/', '', $url);

        $path = new Path('path');
        $path->parse(page(), $url);

        $this->addElement('path', $path->root(), null, $parent);
    }

    public function addParams($kirby, $parent)
    {
        $element = $this->addElement('params', null, null, $parent);

        foreach (params() as $name => $value) {
            $this->addElement($name, $value, null, $element);
        }
    }

    public function addQuery($kirby, $parent)
    {
        $element = $this->addElement('query', null, null, $parent);

        foreach (get() as $key => $value) {
            if ($key === 'data') {
                continue;
            }

            $name = Str::slug($key);

            if (is_array($value)) {
                $value = htmlspecialchars(implode(',', urlencode($value)));
            } else {
                $value = htmlspecialchars(urlencode($value));
            }

            $this->addElement($name, $value, null, $element);
        }
    }

    public function addUser($kirby)
    {
        if ($kirby->user()) {
            $user = new User('user');
            $user->setIncluded(true);
            $user->import($kirby->user());

            $this->addElement('user', $user->root());
        }
    }
}
