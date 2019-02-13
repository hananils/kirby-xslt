<?php

namespace Hananils\Converters;

use Hananils\Converters\User;
use Hananils\Xml;

class Users extends Xml
{
    public function import($users)
    {
        if ($users->isEmpty()) {
            return;
        }

        foreach ($users->children() as $child) {
            $user = new User('user');
            $user->import($child);

            $this->addElement('user', $user->root());
        }
    }
};
