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
            'multilang' => $kirby->multilang() ? 'true' : 'false',
            'version' => $kirby->version()
        ]);

        $this->addNode('urls', $kirby);
        $this->addNode('request', $kirby);
        $this->addNode('languages', $kirby);
        $this->addNode('session', $kirby);
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
        $this->addGet($kirby, $element);
        $this->addPost($kirby, $element);

        /**
         * The query node is deprecated and will be removed in 2.0.0.
         * Please use the get node instead.
         */
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

    /**
     * The query node is deprecated and will be removed in 2.0.0.
     * Please use the get node instead.
     */
    public function addQuery($kirby, $parent)
    {
        $element = $this->addElement('query', null, [
            'warning' => 'deprecated',
            'use' => '/data/kirby/request/get'
        ], $parent);

        foreach (get() as $key => $value) {
            if ($key === 'data') {
                continue;
            }

            $name = Str::slug($key);

            if (is_array($value)) {
                $value = htmlspecialchars(urldecode(implode(', ', $value)));
            } else {
                $value = htmlspecialchars(urldecode($value));
            }

            $this->addElement($name, $value, null, $element);
        }
    }

    public function addGet($kirby, $parent)
    {
        $values = new RecursiveArray('get');
        $values->read($_GET);

        $this->addElement('get', $values->root(), null, $parent);
    }

    public function addPost($kirby, $parent)
    {
        $values = new RecursiveArray('post');
        $values->read($_POST);

        $this->addElement('post', $values->root(), null, $parent);
    }

    public function addLanguages($kirby)
    {
        if ($kirby->languages()->isEmpty()) {
            return;
        }

        $languages = $this->addElement('languages');

        foreach ($kirby->languages()->data() as $code => $language) {
            $item = $this->addElement('language', $language->name(), [
                'code' => $code,
                'direction' => $language->direction(),
                'locale' => $language->locale(),
                'url' => $language->url()
            ], $languages);

            if ($language->isDefault()) {
                $this->addAttribute('default', 'true', $item);
            }

            if ($code === $kirby->language()->code()) {
                $this->addAttribute('current', 'true', $item);
            }
        }
    }

    public function addSession($kirby)
    {
        $session = $this->addElement('session');

        foreach ($kirby->session()->get() as $key => $value) {
            $this->addElement($key, $value, null, $session);
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
