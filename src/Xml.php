<?php

namespace Hananils;

use DomDocument;
use Hananils\Converters\Page;
use Hananils\Definitions\Definitions;
use Kirby\Toolkit\Str;

class Xml
{
    protected $document;
    protected $root;
    protected $name;

    public $included = true;
    public $includedTrue = [];

    public $caching = true;

    public function __construct($root = 'data', $version = '1.0', $encoding = 'utf-8')
    {
        $this->document = new DOMDocument($version, $encoding);
        $this->root = $this->document->createElement($root);
        $this->document->appendChild($this->root);
        $this->name = $root;

        $this->caching = option('hananils.xslt.cache');
    }

    public function document()
    {
        return $this->document;
    }

    public function root()
    {
        return $this->root;
    }

    public function addData($data, $measure = false)
    {
        unset($data['errorCode']);
        unset($data['errorMessage']);
        unset($data['errorType']);
        ksort($data);

        // Get definitions
        $template = page()->template();
        $definitions = new Definitions($template);

        foreach ($data as $name => $object) {
            $start = microtime(true);
            $included = $definitions->get($name);
            $content = null;

            if (empty($object) || $included === false) {
                continue;
            }

            // The type equals the last uppercase word from the object's class name
            $type = preg_replace('/.*([A-Z][a-z]+)$/', '$1', get_class($object));

            switch ($type) {
                case 'App':
                    $content = $this->getContent('Hananils\Converters\Kirby', $name, $included, $object);
                    break;
                case 'Pages':
                    $content = $this->getContent('Hananils\Converters\Pages', $name, $included, $object);
                    break;
                case 'Site':
                case 'Page':
                    $content = $this->getContent('Hananils\Converters\Page', $name, $included, $object);
                    break;
                case 'Files':
                    $content = $this->getContent('Hananils\Converters\Files', $name, $included, $object);
                    break;
                case 'File':
                    $content = $this->getContent('Hananils\Converters\File', $name, $included, $object);
                    break;
                case 'Users':
                    $content = $this->getContent('Hananils\Converters\Users', $name, $included, $object);
                    break;
                case 'User':
                    $content = $this->getContent('Hananils\Converters\User', $name, $included, $object);
                    break;
                case 'Document':
                    $content = $object->documentElement;
                    break;
                case 'Element':
                    $content = $object;
                    break;
            }

            if ($content) {
                $element = $this->addElement($name, $content);

                if ($measure === true) {
                    $end = microtime(true);
                    $this->addAttribute(['https://hananils.de/kirby-xslt', 'hananils', 'execution-time'], round(($end - $start) * 1000, 2), $element);
                }
            }
        }
    }

    public function getContent($class, $name, $included, $object)
    {
        $node = new $class($name);
        $node->setIncluded($included);
        $node->import($object);

        return $node->root();
    }

    public function addElement($name, $content = null, $attributes = null, $context = null)
    {
        if (is_a($content, 'DOMElement') || is_a($content, 'DOMNode')) {
            $element = $this->document->importNode($content, true);
        } else {
            $namespace = null;
            if (is_array($name)) {
                list($namespace, $prefix, $name) = $name;
            }

            $name = Str::slug($name);

            if ($namespace) {
                $element = $this->document->createElementNS($namespace, $prefix . ':' . $name, $this->sanitize($content));
            } else {
                $element = $this->document->createElement($name, $this->sanitize($content));
            }
        }

        $this->addAttributes($attributes, $element);

        if ($context === null) {
            $context = $this->root;
        }

        return $context->appendChild($element);
    }

    public function addAttributes($attributes = [], $element = null, $force = false)
    {
        if (!is_array($attributes)) {
            return;
        }

        if ($element === null) {
            $element = $this->root;
        }

        foreach ($attributes as $name => $value) {
            $this->addAttribute($name, $value, $element, $force);
        }
    }

    public function addAttribute($name, $value, $element = null, $force = false)
    {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        if (!$force && empty(trim($value)) && $value !== 0) {
            return;
        }

        $namespace = null;
        if (is_array($name)) {
            list($namespace, $prefix, $name) = $name;
        }

        $name = Str::slug($name);

        if ($namespace) {
            $attribute = $this->document->createAttributeNS($namespace, $prefix . ':' . $name);
        } else {
            $attribute = $this->document->createAttribute($name);
        }

        $attribute->value = $this->sanitize($value);

        if ($element === null) {
            $element = $this->root;
        }

        return $element->appendChild($attribute);
    }

    public function setIncluded($included)
    {
        if (empty($included)) {
            return;
        }

        if ($included === true) {
            if (!empty($this->includedTrue)) {
                $included = $this->includedTrue;
            }
        }

        if (is_array($included)) {
            if (is_array($this->included)) {
                $this->included = array_replace_recursive($this->included, $included);
            } else {
                $this->included = $included;
            }
        }
    }

    public function addNode($name, $context = null)
    {
        $handler = 'add' . Str::ucfirst($name);

        if ($this->included === true || (isset($this->included[$name]) && ($this->included[$name] === true || is_array($this->included[$name])))) {
            $this->$handler($context);
        }
    }

    public function addNodeAttributes($context = null)
    {
        if (!isset($this->included['attributes']) || !$context) {
            return;
        }

        $names = $this->included['attributes'];

        foreach ($names as $name) {
            $slug = Str::slug($name);
            $name = str_replace('-', '', ucwords($slug, '-'));

            if (method_exists($context, $name)) {
                $value = $context->$name();

                if (is_bool($value)) {
                    if ($value === true) {
                        $value = 'true';
                    } else {
                        $value = 'false';
                    }
                } elseif (is_array($value)) {
                    $value = implode(',', $value);
                }

                $this->addAttribute($slug, $value);
            }
        }
    }

    public function sanitize($string)
    {
        return htmlspecialchars($string, ENT_COMPAT, 'UTF-8', false);
    }

    public function generate()
    {
        return $this->document->saveXml();
    }

}
