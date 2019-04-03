<?php

namespace Hananils\Converters\Fields;

use Hananils\CacheAssociative as Cache;
use Hananils\Converters\User;
use Hananils\Xml;

class Users extends Xml
{
    public function parse($field, $blueprint)
    {
        if ($field->isEmpty()) {
            return;
        }

        foreach ($field->toUsers() as $child) {
            $user = new User('user');
            $user->import($child);

            $this->addElement('user', $user->root());

            Cache::setAssociation($child, $field->parent());
        }
    }
}
