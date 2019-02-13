<?php

namespace Hananils\Converters\Utilities;

use Hananils\Xml;

class Email extends Xml
{
    public function parse($email, $name = 'email')
    {
        $this->root->nodeValue = $email;

        $parts = explode('@', $email);
        if (count($parts) > 1) {
            $attributes = [
                'alias' => $parts[0],
                'domain' => $parts[1],
                'hash' => md5(strtolower($email))
            ];

            $this->addAttributes($attributes);
        }
    }
}
