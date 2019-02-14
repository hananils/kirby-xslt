<?php

namespace Hananils\Converters;

use Hananils\Converters\File;
use Hananils\Xml;

class Files extends Xml
{
    private $wrapper;

    public function import($files)
    {
        if ($files->isEmpty()) {
            return;
        }

        $files = $files->filter(function ($file) {
            return !empty($file->template());
        });

        foreach ($files->groupBy('template') as $group => $children) {
            $this->wrapper = $this->addElement($group);

            if (isset($this->included) && ($this->included === true || array_key_exists($group, $this->included))) {
                $included = $this->included[$group];

                foreach ($children->sortBy('sort') as $child) {
                    $this->addFile($child, $included);
                }
            }
        }
    }

    private function addFile($child, $included = true)
    {
        $file = new File('file');
        $file->setIncluded($included);
        $file->import($child);

        $this->addElement('file', $file->root(), null, $this->wrapper);
    }
}
