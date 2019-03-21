<?php

namespace Hananils\Converters;

use Hananils\Xml;

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
        'thumbs' => true
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
        $meta->parse($file->meta(), $file->blueprint()->fields(), $file->errors());

        $this->addElement('meta', $meta->root());
    }

    public function addThumbs($file)
    {
        $thumbs = $this->addElement('thumbs');

        foreach ($this->included['thumbs'] as $attributes) {
            if (array_key_exists('crop', $attributes) && strpos($attributes['crop'], 'fields.') === 0) {
                $field = explode('.', $attributes['crop'])[1];
                $crop = $file->content()->get($field)->toString();

                if ($crop) {
                    $attributes['crop'] = $crop;
                } else {
                    $attributes['crop'] = 'center';
                }
            }

            $this->addElement('url', $file->thumb($attributes)->url(), $attributes, $thumbs);
        }
    }

}
