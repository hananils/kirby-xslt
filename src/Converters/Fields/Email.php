<?php

namespace Hananils\Converters\Fields;

use Hananils\Converters\Utilities\Email as EmailParser;
use Hananils\Xml;

class Email extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        $email = new EmailParser('email');
        $email->parse($field->toString());

        $this->root = $email->root();
    }
}
