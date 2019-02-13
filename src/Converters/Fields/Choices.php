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

        $options = $this->getOptions($blueprint);

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

    private function getOptions($blueprint)
    {
        $options = null;

        if (array_key_exists('options', $blueprint)) {
            $options = $blueprint['options'];
        }

        if ($options === 'query') {
            $references = Options::query($blueprint['query']);

            if (is_array($references)) {
                $options = [];
                foreach ($references as $reference) {
                    $options[Str::slug($reference['value'])] = $reference['text'];
                }
            }
        }

        return $options;
    }
}
