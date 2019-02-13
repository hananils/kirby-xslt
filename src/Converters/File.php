<?php

namespace Hananils\Converters;

use Hananils\Xml;

class File extends Xml
{
    public function import($file)
    {
        $this->addAttributes([
            'size' => $file->size(),
            'nice-size' => $file->niceSize(),
            'type' => $file->type(),
            'mime' => $file->mime(),
            'extension' => $file->extension(),
            'url' => $file->url()
        ]);

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
        $meta->parse($file->meta(), $file->blueprint()->fields());

        $this->addElement('meta', $meta->root());
    }

    public function addThumbs($file)
    {
        $thumbs = $this->addElement('thumbs');

        foreach ($this->included['thumbs'] as $attributes) {
            if (array_key_exists('crop', $attributes) && strpos($attributes['crop'], 'field.') === 0) {
                $field = explode('.', $attributes['crop'])[1];
                $crop = $file->content()->get($field)->value();

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
