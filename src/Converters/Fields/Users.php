<?php

namespace Hananils\Converters\Fields;

use Hananils\Xml;

class Users extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        foreach ($field->toPages() as $child) {
            $user = new User('user');
            $user->import($child);

            $this->addElement('user', $user->root());
        }

    }
}
