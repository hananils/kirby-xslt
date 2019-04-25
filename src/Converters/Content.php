<?php

namespace Hananils\Converters;

use Hananils\Converters\Fields\Builder;
use Hananils\Converters\Fields\Choices;
use Hananils\Converters\Fields\ColorPalette;
use Hananils\Converters\Fields\CropSelect;
use Hananils\Converters\Fields\Date;
use Hananils\Converters\Fields\Email;
use Hananils\Converters\Fields\Files;
use Hananils\Converters\Fields\Focus;
use Hananils\Converters\Fields\Pages;
use Hananils\Converters\Fields\Radio;
use Hananils\Converters\Fields\Range;
use Hananils\Converters\Fields\Structure;
use Hananils\Converters\Fields\Tel;
use Hananils\Converters\Fields\Text;
use Hananils\Converters\Fields\Textarea;
use Hananils\Converters\Fields\Time;
use Hananils\Converters\Fields\Toggle;
use Hananils\Converters\Fields\Unknown;
use Hananils\Converters\Fields\Url;
use Hananils\Converters\Fields\Users;
use Hananils\Xml;

class Content extends Xml
{
    protected $ignored = ['headline', 'info', 'line'];

    public function parse($content, $fields, $context = null)
    {
        // Errors
        if ($context && isset($this->included['errors']) && $this->included['errors'] === true) {
            $errors = $context->errors();

            $validation = $this->addElement('errors', null, [
                'type' => 'validation',
                'count' => count($errors),
                'note' => 'Please note that validation is done on request which will decrease performance on large collections.'
            ]);

            foreach ($errors as $field => $error) {
                $item = $this->addElement($field, null, [
                    'label' => $error['label'],
                    'type' => 'invalid'
                ], $validation);

                foreach ($error['message'] as $type => $message) {
                    $this->addElement($type, $message, null, $item);
                }
            }
        }

        // Content
        foreach ($fields as $name => $blueprint) {
            $input = null;

            if (is_array($this->included) && !array_key_exists($name, $this->included)) {
                continue;
            }

            switch ($blueprint['type']) {
                case 'hidden':
                case 'number':
                case 'text':
                    $input = new Text($name);
                    break;
                case 'textarea':
                    $input = new Textarea($name);
                    break;
                case 'checkboxes':
                case 'multiselect':
                case 'select':
                case 'tags':
                    $input = new Choices($name);
                    break;
                case 'radio':
                case 'imageradio':
                    $input = new Radio($name);
                    break;
                case 'toggle':
                    $input = new Toggle($name);
                    break;
                case 'range':
                    $input = new Range($name);
                    break;
                case 'tel':
                    $input = new Tel($name);
                    break;
                case 'time':
                    $input = new Time($name);
                    break;
                case 'date':
                    $input = new Date($name);
                    break;
                case 'url':
                    $input = new Url($name);
                    break;
                case 'email':
                    $input = new Email($name);
                    break;
                case 'structure':
                    $input = new Structure($name);
                    break;
                case 'pages':
                    $input = new Pages($name);
                    break;
                case 'files':
                    $input = new Files($name);
                    break;
                case 'users':
                    $input = new Users($name);
                    break;
                case 'color-palette':
                    $input = new ColorPalette($name);
                    break;
                case 'cropselect':
                    $input = new CropSelect($name);
                    break;
                case 'builder':
                    $input = new Builder($name);
                    break;
                case 'focus':
                    $input = new Focus($name);
                    break;
                default:
                    $input = new Unknown($name);
                    break;
            }

            if (is_null($input)) {
                continue;
            }

            $field = $content->get($name);
            $input->setIncluded($this->included[$name]);
            $input->parse($field, $blueprint);

            $this->addElement($name, $input->root());
        }
    }
}
