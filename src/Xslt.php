<?php

namespace Hananils;

use DOMDocument;
use DOMXPath;
use Exception;
use Hananils\Xml;
use Kirby\Cms\App;
use Kirby\Cms\Template;
use XSLTProcessor;

class Xslt extends Template
{
    private $xml;
    private $errors;

    public function extension(): string
    {
        return 'xsl';
    }

    public function measure()
    {
        return get('data') !== null;
    }

    public function displayData()
    {
        return get('data') !== null && kirby()->user();
    }

    public function render(array $data = []): string
    {
        if ($this->exists() === false) {
            throw new Exception($this->missingViewMessage());
        }

        // Convert data
        $this->xml = new Xml();
        $this->xml->addData($data, $this->measure());

        // Render
        if ($this->displayData()) {
            return $this->renderData();
        } else {
            return $this->renderTemplate();
        }
    }

    public function renderTemplate()
    {
        return $this->transform($this->xml, $this->file());
    }

    public function renderData()
    {
        if (get('xpath')) {
            $this->setMatches();
        }

        $this->setContext([
            'index' => kirby()->url('index'),
            'media' => kirby()->url('media'),
            'page' => preg_replace('/\/?\?.*/', '', kirby()->request()->url()),
            'site' => site()->title(),
            'title' => page()->title(),
            'find-by-xpath' => t('xpath', 'find by XPath'),
            'search' => t('search', 'search'),
            'open' => t('open', 'open'),
            'data' => t('data', 'data'),
            'overview' => t('overview', 'Overview'),
            'execution' => t('execution', 'General execution time')
        ]);

        if (get('data') === 'raw') {
            kirby()->response()->type('xml');
            $result = $this->xml->document()->saveXML();
        } else {
            $this->addIcons();
            $result = $this->transform($this->xml, App::instance()->root('plugins') . '/xslt/templates/debug.xsl');
        }

        return $result;
    }

    public function renderErrors()
    {
        $data = new Xml();
        $errors = $data->addElement('errors');

        foreach ($this->errors as $error) {
            preg_match('/line (\d+)/', $error->message, $ref);

            $data->addElement('error', $error->message, [
                'level' => $error->level,
                'code' => $error->code,
                'column' => $error->column,
                'file' => $error->file,
                'line' => $error->line,
                'referenced-line' => isset($ref[1]) ? $ref[1] : ''
            ], $errors);
        }

        $source = fopen($this->errors[1]->file, "r");
        if ($source) {
            $file = $data->addElement('file');

            $index = 1;
            while (($line = fgets($source)) !== false) {
                $data->addElement('line', $line, null, $file);
                $index++;
            }

            fclose($source);
        }

        $data->addAttribute(['https://hananils.de/kirby-xslt', 'hananils', 'media'], kirby()->url('media'));

        return $this->transform($data, App::instance()->root('plugins') . '/xslt/templates/debug.xsl');
    }

    public function transform($xml, $xsl)
    {
        libxml_disable_entity_loader(false);
        libxml_use_internal_errors(true);

        try {
            $stylesheet = new DOMDocument();
            $stylesheet->load($xsl);

            $xslt = new XSLTProcessor();
            $xslt->importStylesheet($stylesheet);

            $result = $xslt->transformToXML($xml->document());
            $result = str_replace('<!DOCTYPE html SYSTEM "about:legacy-compat">', '<!DOCTYPE html>', $result);
        } catch (Exception $e) {
            if (!$this->errors && kirby()->user()) {
                $this->errors = libxml_get_errors();
                libxml_clear_errors();

                return $this->renderErrors();
            } else {
                return ':(';
            }
        }

        return $result;
    }

    private function setContext($data)
    {
        foreach ($data as $key => $value) {
            $this->xml->addAttribute(['https://hananils.de/kirby-xslt', 'hananils', $key], $value);
        }
    }

    private function setMatches()
    {
        $handling = libxml_use_internal_errors(true);

        $xpath = new DOMXPath($this->xml->document());
        $matches = $xpath->query(get('xpath'));

        libxml_use_internal_errors($handling);

        if ($matches) {
            foreach ($matches as $match) {
                $this->xml->addAttribute(['https://hananils.de/kirby-xslt', 'hananils', 'xpath-matched'], 'true', $match);
            }
        }
    }

    private function addIcons()
    {
        $svg = new Xml('svg');
        $svg->document()->load(kirby()->root('kirby') . '/panel/public/img/icons.svg');
        $icons = $this->xml->addElement(['https://hananils.de/kirby-xslt', 'hananils', 'kirby-icons']);
        $this->xml->addElement('svg', $svg->document()->documentElement, null, $icons);
    }

}
