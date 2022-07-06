<?php

namespace Hananils\Converters;

use Hananils\Xml;
use Kirby\Filesystem\Dir;

class Directory extends Xml
{
    public function read($folder, $context = null)
    {
        foreach (Dir::read($folder) as $item) {
            $path = $folder . '/' . $item;

            if (is_file($path)) {
                $this->addElement('file', htmlspecialchars($item), [
                    'modified' => filemtime($path),
                    'mime' => mime_content_type($path),
                    'extension' => pathinfo($path, PATHINFO_EXTENSION)
                ], $context);
            } else {
                $directory = $this->addElement($item, null, null, $context);
                $this->read($path, $directory);
            }
        }
    }
}
