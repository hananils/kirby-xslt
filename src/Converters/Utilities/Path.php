<?php

namespace Hananils\Converters\Utilities;

use Hananils\Xml;

class Path extends Xml
{
    public function parse($page, $url = null)
    {
        if ($url === null) {
            $url = $page->uri();
        }

        $url = str_replace(kirby()->urls()->index(), '', $url);
        $path = array_filter(explode('/', $url));
        $step = '';

        foreach ($path as $param) {
            $step .= '/' . $param;
            $relative = kirby()->page(substr($step, 1));
            $attributes = null;

            if ($relative) {
                $attributes = [
                    'template' => $relative->template()->name(),
                    'title' => htmlspecialchars($relative->title())
                ];
            }

            $this->addElement('param', $param, $attributes);
        }

        $this->addAttribute(
            'url',
            kirby()->urls()->index() . $step
        );
    }
}
