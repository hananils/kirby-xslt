<?php

namespace Hananils\Converters;

use Hananils\Converters\Content;
use Hananils\Converters\Utilities\Email;
use Hananils\Xml;

class User extends Xml
{
    public $included = [
        'attributes' => ['id'],
        'username' => true,
        'email' => false,
        'avatar' => false,
        'content' => false
    ];

    public $includedTrue = [
        'attributes' => ['id', 'language', 'role'],
        'username' => true,
        'email' => true,
        'avatar' => true,
        'content' => true
    ];

    public function import($user)
    {
        $this->addNodeAttributes($user);

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
        $content->parse($user->content(), $user->blueprint()->fields(), $user);

        $this->addElement('content', $content->root());
    }

}
