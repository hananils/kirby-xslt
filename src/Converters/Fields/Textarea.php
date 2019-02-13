<?php

namespace Hananils\Converters\Fields;

use DOMDocument;
use Hananils\Xml;

class Textarea extends Xml {
    public function parse($field, $blueprint) {
        if ($field->isEmpty()) {
            return;
        }

        if ($this->included === 'unformatted') {
            $this->addUnformatted($field);
        } else {
            $this->addFormatted($field, $this->included);
        }
    }

    public function addUnformatted($field) {
        $this->addAttribute('format', 'unformatted');
        $cdata = $this->document->createCDATASection($field->toString());
        $this->root->appendChild($cdata);
    }

    public function addFormatted($field, $format) {
        if ($format === 'markdown') {
            $html = $field->markdown();
        } else {
            $format = 'kirbytext';
            $html = $field->kirbytext();
        }

        $handling = libxml_use_internal_errors(true);

        $tag = $this->root->tagName;
        $xml = new DOMDocument();

        if ($xml->loadXML('<' . $tag . ' format="' . $format . '">' . $html . '</' . $tag . '>')) {
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
