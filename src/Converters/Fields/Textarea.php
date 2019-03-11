<?php

namespace Hananils\Converters\Fields;

use DOMDocument;
use Hananils\Xml;

class Textarea extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        if ($this->included === 'unformatted') {
            $this->addUnformatted($field);
        } else {
            $this->addFormatted($field, $this->included);
        }
    }

    public function addUnformatted($field)
    {
        $this->addAttribute('format', 'unformatted');
        $cdata = $this->document->createCDATASection($field->toString());
        $this->root->appendChild($cdata);
    }

    public function addFormatted($field, $format)
    {
        if ($format === 'markdown') {
            $html = $field->markdown();
        } else {
            $format = 'kirbytext';
            $html = $field->kirbytext();
        }

        // Clean-up
        $html = str_replace('allowfullscreen', 'allowfullscreen="true"', $html);
        $html = str_replace('class="video"', 'class="m-video"', $html);
        $html = str_replace('src="./', 'src="', $html);

        $handling = libxml_use_internal_errors(true);

        $tag = $this->root->tagName;
        $xml = new DOMDocument('1.0', 'utf-8');

        if ($xml->loadHTML('<?xml encoding="UTF-8"><' . $tag . ' format="' . $format . '">' . $html . '</' . $tag . '>', LIBXML_HTML_NOIMPLIED)) {
            $this->root = $xml->documentElement;
        } else {
            $error = $this->addElement('error', null, [
                'type' => 'invalid'
            ]);
            $cdata = $this->document->createCDATASection($html);
            $error->appendChild($cdata);
        }

        libxml_use_internal_errors($handling);
    }
}
