<?php

namespace Hananils;

use DOMDocument;
use DOMXPath;
use Exception;
use Hananils\Converters\Kirby;
use Hananils\Converters\Page;
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
            throw new Exception('The view does not exist: ' . $this->name());
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
        if (get('data') === 'raw') {
            kirby()
                ->response()
                ->type('xml');
            $result = $this->xml->document()->saveXML();
        } else {
            $this->setMatches();
            $this->addPluginElement();
            $result = $this->transform(
                $this->xml,
                App::instance()->root('plugins') . '/xslt/templates/data.xsl'
            );
        }

        return $result;
    }

    public function renderErrors()
    {
        $errors = $this->xml->addElement([
            'https://hananils.de/kirby-xslt',
            'hananils',
            'kirby-xslt-errors'
        ]);

        foreach ($this->errors as $error) {
            preg_match('/line (\d+)/', $error->message, $ref);

            $this->xml->addElement(
                'error',
                $error->message,
                [
                    'level' => $error->level,
                    'code' => $error->code,
                    'column' => $error->column,
                    'file' => $error->file,
                    'line' => $error->line,
                    'referenced-line' => isset($ref[1]) ? $ref[1] : ''
                ],
                $errors
            );
        }

        $file = null;
        if (!empty($this->errors[0]->file)) {
            $file = $this->errors[0]->file;
        } elseif (
            $this->errors &&
            preg_match('/file (.*) line/', $this->errors[0]->message, $matches)
        ) {
            $file = $matches[1];
        }

        if ($file) {
            $source = fopen($file, 'r');
            if ($source) {
                $file = $this->xml->addElement('file', null, null, $errors);

                $index = 1;
                while (($line = fgets($source)) !== false) {
                    $this->xml->addElement('line', $line, null, $file);
                    $index++;
                }

                fclose($source);
            }
        }

        $this->addPluginElement();

        $result = $this->transform(
            $this->xml,
            App::instance()->root('plugins') . '/xslt/templates/data.xsl'
        );

        return $result;
    }

    public function transform($xml, $xsl)
    {
        libxml_use_internal_errors(true);

        try {
            $stylesheet = new DOMDocument();
            $stylesheet->load($xsl);

            $xslt = new XSLTProcessor();
            $xslt->importStylesheet($stylesheet);

            $result = $xslt->transformToXML($xml->document());
            $result = str_replace(
                '<!DOCTYPE html SYSTEM "about:legacy-compat">',
                '<!DOCTYPE html>',
                $result
            );
        } catch (Exception $e) {
            if (!$this->errors && kirby()->user()) {
                $this->errors = libxml_get_errors();
                libxml_clear_errors();

                return $this->renderErrors();
            } else {
                require_once kirby()->root('kirby') . '/views/fatal.php';
                die();
            }
        }

        return $result;
    }

    private function addPluginElement()
    {
        $plugin = $this->xml->addElement([
            'https://hananils.de/kirby-xslt',
            'hananils',
            'kirby-xslt'
        ]);
        $this->xml->addAttribute(
            'cache',
            option('hananils.kirby-xslt.cache') === true ? 'true' : 'false',
            $plugin
        );

        /* Add kirby node */
        $kirby = new Kirby('kirby');
        $kirby->import(kirby());
        $this->xml->addElement('kirby', $kirby->root(), null, $plugin);

        /* Add site node */
        $page = new Page('site');
        $page->import(site());
        $this->xml->addElement('site', $page->root(), null, $plugin);

        /* Add current page node */
        $page = new Page('page');
        $page->import(page());
        $this->xml->addElement('page', $page->root(), null, $plugin);

        /* Add dictionary */
        $dictionary = $this->xml->addElement('dictionary', null, null, $plugin);
        $language = 'en';
        if (
            kirby()
            ->languages()
            ->count()
        ) {
            $language = kirby()
                ->language()
                ->code();
        }

        $translations = kirby()
            ->plugin('hananils/kirby-xslt')
            ->extends()['translations'][$language];
        foreach ($translations as $key => $translation) {
            $this->xml->addElement($key, $translation, null, $dictionary);
        }

        /* Add icons */
        $icons = $this->xml->addElement('icons', null, null, $plugin);
        $svg = new Xml('svg');
        $svg->document()->load(
            kirby()->root('kirby') . '/panel/public/img/icons.svg'
        );
        $this->xml->addElement(
            'svg',
            $svg->document()->documentElement,
            null,
            $icons
        );

        /* Add XSLT processing time of frontend template */
        if (!$this->errors) {
            $start = microtime(true);
            $this->renderTemplate();
            $end = microtime(true);
            $this->xml->addAttribute(
                'transformation-time',
                round(($end - $start) * 1000, 2),
                $plugin
            );
        }
    }

    private function setMatches()
    {
        if (!get('xpath')) {
            return;
        }

        $handling = libxml_use_internal_errors(true);

        $xpath = new DOMXPath($this->xml->document());
        $matches = $xpath->query(get('xpath'));

        libxml_use_internal_errors($handling);

        if ($matches) {
            foreach ($matches as $match) {
                $this->xml->addAttribute(
                    [
                        'https://hananils.de/kirby-xslt',
                        'hananils',
                        'xpath-matched'
                    ],
                    'true',
                    $match
                );
            }
        }
    }
}
