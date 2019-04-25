<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;
use Kirby\Form\Options;
use Kirby\Toolkit\Str;

class Radio extends Xml
{
    static $cache = [];

    public static function setCache($key, $value)
    {
        if (!empty($key) && !empty($value)) {
            self::$cache[$key] = $value;
        }
    }

    public static function getCache($key)
    {
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        return null;
    }

    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $slug = $field->toString();
        $options = $this->getOptions($field, $blueprint);

        if (is_array($options) && array_key_exists($slug, $options)) {
            $value = $options[$slug];
        }

        if (is_array($value)) {
            if (isset($value['text'])) {
                $value = $value['text'];
            } else {
                $value = implode(', ', $value);
            }
        }

        $this->addAttribute('key', $slug);
        $this->root->nodeValue = $this->sanitize($value);
    }

    private function getOptions($field, $blueprint)
    {
        $options = null;
        $references = null;

        if (array_key_exists('options', $blueprint)) {
            $options = $blueprint['options'];
        }

        if ($options === 'query') {
            $references = $this->getByQuery($blueprint, $field);
        } elseif ($options === 'api') {
            $references = $this->getByApi($blueprint, $field);
        }

        if (is_array($references)) {
            $options = [];
            foreach ($references as $reference) {
                $options[Str::slug($reference['value'])] = $reference['text'];
            }
        }

        return $options;
    }

    private function getByQuery($blueprint, $field)
    {
        if (!isset($blueprint['query']['fetch'])) {
            return [];
        }

        $id = $blueprint['query']['fetch'];
        $id = preg_replace('/^page.siblings/', $field->parent()->parent()->id(), $id);
        $id = preg_replace('/^page/', $field->parent()->parent(), $id);
        $id = Str::slug($id);

        $references = self::getCache($id);

        if (!$references) {
            $references = Options::query($blueprint['query'], $field->parent());
            self::setCache($id, $references);
        }

        return $references;
    }

    private function getByApi($blueprint, $field)
    {
        $id = Str::slug('api_' . $blueprint['api']['url']);
        $references = self::getCache($id);

        if (!$references) {
            $references = Options::api($blueprint['api'], $field->parent());
            self::setCache($id, $references);
        }

        return $references;
    }

};
