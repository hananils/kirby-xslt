<?php

namespace Hananils;

use DOMDocument;
use Hananils\Xml;
use Kirby\Cms\App;
use Kirby\Cms\Template;
use XSLTProcessor;

class Xslt extends Template {
    public function extension(): string {
        return 'xsl';
    }

    public function render(array $data = []): string {
        if ($this->exists() === false) {
            throw new Exception($this->missingViewMessage());
        }

        // Convert data
        $xml = new Xml();
        $xml->addData($data);

        // Render
        if (isset($_GET['data']) && kirby()->user()) {
            return $this->transform($xml, App::instance()->root('plugins') . '/xslt/templates/debug.xsl');
        } else {
            return $this->transform($xml, $this->file());
        }
    }

    public function transform($xml, $xsl) {
        libxml_disable_entity_loader(false);
        libxml_use_internal_errors(true);

        $stylesheet = new DOMDocument();
        $stylesheet->load($xsl);

        $xslt = new XSLTProcessor();
        $xslt->importStylesheet($stylesheet);

        $result = $xslt->transformToXML($xml->document());
        $result = str_replace('<!DOCTYPE html SYSTEM "about:legacy-compat">', '<!DOCTYPE html>', $result);

        return $result;
    }
}
