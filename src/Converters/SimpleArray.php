<?php

namespace Hananils\Converters;

use Hananils\Xml;
use Kirby\Toolkit\Str;

class SimpleArray extends Xml
{
    public function read($array, $context = null)
    {
        foreach ($array as $item) {
            $this->addElement(
                'item',
                htmlspecialchars($item),
                [
                    'slug' => Str::slug($item)
                ],
                $context
            );
        }
    }
}
