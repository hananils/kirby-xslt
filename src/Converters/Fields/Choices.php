<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;
use Kirby\Form\Options;
use Kirby\Toolkit\Str;

class Choices extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $options = $this->getOptions($field, $blueprint);

        foreach ($field->toData() as $value) {
            $slug = Str::slug($value);

            if (is_array($options) && array_key_exists($slug, $options)) {
                $value = $options[$slug];
            }

            $this->addElement('item', $value, [
                'slug' => $slug
            ]);
        }
    }

    private function getOptions($field, $blueprint)
    {
        $options = null;
        $references = null;

        if (array_key_exists('options', $blueprint)) {
            $options = $blueprint['options'];
        }

        if ($options === 'query') {
            $references = Options::query($blueprint['query'], $field->parent());
        } elseif ($options === 'api') {
            $references = Options::api($blueprint['api'], $field->parent());
        }

        if (is_array($references)) {
            $options = [];
            foreach ($references as $reference) {
                $options[Str::slug($reference['value'])] = $reference['text'];
            }
        }

        return $options;
    }
};
