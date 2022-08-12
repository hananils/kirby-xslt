<?php

namespace Hananils\Converters;

use Hananils\Xml;
use Kirby\Toolkit\Str;

class File extends Xml
{
    public $included = [
        'attributes' => ['type', 'url'],
        'filename' => true,
        'meta' => false,
        'thumbs' => false
    ];

    public $includedTrue = [
        'attributes' => ['type', 'url'],
        'filename' => true,
        'meta' => true,
        'thumbs' => []
    ];

    public function import($file)
    {
        $this->addNodeAttributes($file);

        $this->addNode('filename', $file);
        $this->addNode('meta', $file);
        $this->addNode('thumbs', $file);
    }

    public function addFilename($file)
    {
        $this->addElement('filename', $file->filename());
    }

    public function addMeta($file)
    {
        $meta = new Content('meta');
        $meta->setIncluded($this->included['meta']);
        $meta->parse($file->content(), $file->blueprint()->fields(), $file);

        $this->addElement('meta', $meta->root());
    }

    public function addThumbs($file)
    {
        $thumbs = $this->addElement('thumbs');

        if (is_string($this->included['thumbs'])) {
            // Get attributes from preset
        }

        foreach ($this->included['thumbs'] as $options) {
            $type = null;

            if (isset($options['crop']) && strpos($options['crop'], '.') > 0) {
                $positions = [
                    "top left", "top", "top right", "left", "center", "right", "bottom left", "bottom", "bottom right"
                ];

                $name = explode('.', $options['crop'])[1];
                $field = $file->content()->get($name);
                $type = $file->blueprint()->field($name)['type'];

                if ($type !== 'focus' && in_array($field->value(), $positions)) {
                    $options['crop'] = $field->value();
                } else {
                    $options['crop'] = 'center';
                }
            }

            if ($type === 'focus') {
                $width = isset($options['width']) ? $options['width'] : null;
                $height = isset($options['height']) ? $options['height'] : null;
                $thumb = $file->focusCrop($width, $height, $options);
            } else {
                $thumb = $file->thumb($options);
            }

            $attributes = [];
            foreach ($thumb->modifications() as $key => $value) {
                $key = Str::kebab($key);
                if (is_bool($value)) {
                    $attributes[$key] = ($value === true ? 'true' : 'false');
                } else {
                    $attributes[$key] = $value;
                }
            }

            if(!isset($attributes['height']) && isset($width)) {
                $attributes['height'] = $width / $file->width() * $file->height();
            } else {
                $attributes['height'] = $file->height();
            }

            if(!isset($attributes['width']) && isset($height)) {
                $attributes['width'] = $height / $file->height() * $file->width();
            } else {
                $attributes['width'] = $file->width();
            }

            $this->addElement('url', $thumb->url(), $attributes, $thumbs);
        }
    }

}
