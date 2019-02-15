<?php

namespace Hananils;

use DomDocument;
use Hananils\Converters\File;
use Hananils\Converters\Files;
use Hananils\Converters\Kirby;
use Hananils\Converters\Page;
use Hananils\Converters\Pages;
use Hananils\Converters\User;
use Hananils\Converters\Users;
use Hananils\Definitions\Definitions;
use Kirby\Toolkit\Str;

class Xml
{
    protected $document;
    protected $root;

    public $included = true;

    public function __construct($root = 'data', $version = '1.0', $encoding = 'utf-8')
    {
        $this->document = new DOMDocument($version, $encoding);
        $this->root = $this->document->createElement($root);
        $this->document->appendChild($this->root);
    }

    public function document()
    {
        return $this->document;
    }

    public function root()
    {
        return $this->root;
    }

    public function addData($data)
    {
        unset($data['errorCode']);
        unset($data['errorMessage']);
        unset($data['errorType']);
        ksort($data);

        // Get definitions
        $template = page()->template();
        $definitions = new Definitions($template);

        foreach ($data as $name => $object) {
            $included = $definitions->get($name);
            $content = null;

            if (empty($object) || $included === false) {
                continue;
            }

            // The type equals the last uppercase word from the object's class name
            $type = preg_replace('/.*([A-Z][a-z]+)$/', '$1', get_class($object));

            switch ($type) {
                case 'App':
                    $node = new Kirby($name);
                    $node->import($object);
                    $content = $node->root();
                    break;
                case 'Pages':
                    $node = new Pages($name);
                    if (!empty($included)) {
                        $node->setIncluded($included);
                    }
                    $node->import($object);
                    $content = $node->root();
                    break;
                case 'Site':
                case 'Page':
                    $node = new Page($name);
                    if (!empty($included)) {
                        $node->setIncluded($included);
                    }
                    $node->import($object);
                    $content = $node->root();
                    break;
                case 'Files':
                    $node = new Files($name);
                    if (!empty($included)) {
                        $node->setIncluded($included);
                    }
                    $node->import($object);
                    $content = $node->root();
                    break;
                case 'File':
                    $node = new File($name);
                    if (!empty($included)) {
                        $node->setIncluded($included);
                    }
                    $node->import($object);
                    $content = $node->root();
                    break;
                case 'Users':
                    $node = new Users($name);
                    if (!empty($included)) {
                        $node->setIncluded($included);
                    }
                    $node->import($object);
                    $content = $node->root();
                    break;
                case 'User':
                    $node = new User($name);
                    if (!empty($included)) {
                        $node->setIncluded($included);
                    }
                    $node->import($object);
                    $content = $node->root();
                    break;
                case 'Document':
                    $content = $object->documentElement;
                    break;
                case 'Element':
                    $content = $object;
                    break;
            }

            if ($content) {
                $this->addElement($name, $content);
            }
        }
    }

    public function addElement($name, $content = null, $attributes = null, $context = null)
    {
        if (is_a($content, 'DOMElement') || is_a($content, 'DOMNode')) {
            $element = $this->document->importNode($content, true);
        } else {
            $name = Str::slug($name);
            $element = $this->document->createElement($name, $this->sanitize($content));
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
        if (!$force && empty(trim($value))) {
            return;
        }

        $name = Str::slug($name);
        $attribute = $this->document->createAttribute($name);
        $attribute->value = $this->sanitize($value);

        if ($element === null) {
            $element = $this->root;
        }

        return $element->appendChild($attribute);
    }

    public function setIncluded($included)
    {
        if (is_array($this->included) && is_array($included)) {
            $this->included = array_replace_recursive($this->included, $included);
        } else {
            $this->included = $included;
        }
    }

    public function addNode($name, $context)
    {
        $handler = 'add' . Str::ucfirst($name);

        if ($this->included === true || (isset($this->included[$name]) && ($this->included[$name] === true || is_array($this->included[$name])))) {
            $this->$handler($context);
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
