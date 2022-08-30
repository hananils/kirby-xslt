<?php

namespace Hananils\Converters;

use Hananils\Xml;
use Kirby\Toolkit\Str;

class RecursiveArray extends Xml
{
    public function read($array, $context = null, $index = false)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $parent = $this->addElement($key, null, null, $context);
                $this->read($value, $parent, $this->needsIndex($value));
            } else {
                $attributes = [
                    'slug' => Str::slug($value)
                ];

                if (is_numeric($key)) {
                    if ($index) {
                        $attributes['index'] = $key;
                    }

                    $key = 'item';
                } elseif ($key !== Str::slug($key)) {
                    $attributes['name'] = $key;
                }

                $this->addElement(
                    $key,
                    htmlspecialchars($value),
                    $attributes,
                    $context
                );
            }
        }
    }

    private function needsIndex($array)
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
