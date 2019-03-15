<?php

namespace Hananils\Converters;

use Hananils\Converters\Content;
use Hananils\Converters\Utilities\Email;
use Hananils\Xml;

class User extends Xml
{
    public $included = [
        'username' => true,
        'email' => false,
        'avatar' => false,
        'content' => false
    ];

    public function import($user)
    {
        $this->addAttributes([
            'id' => $user->id(),
            'language' => $user->language(),
            'role' => $user->role()
        ]);

        $this->addNode('username', $user);
        $this->addNode('email', $user);
        $this->addNode('avatar', $user);
        $this->addNode('content', $user);
    }

    public function addUsername($user)
    {
        $this->addElement('username', $user->username());
    }

    public function addEmail($user)
    {
        $email = new Email('email');
        $email->parse($user->email());

        $this->addElement('email', $email->root());
    }

    public function addAvatar($user)
    {
        $this->addElement('avatar');
    }

    public function addContent($user)
    {
        $content = new Content('content');
        $content->parse($user->content(), $user->blueprint()->fields(), $user->errors());

        $this->addElement('content', $content->root());
    }

}
