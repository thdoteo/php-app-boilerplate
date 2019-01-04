<?php

namespace App\Auth\Twig;

use Framework\Auth;

class AuthExtension extends \Twig_Extension
{
    /**
     * @var Auth
     */
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('current_user', [$this->auth, 'getUser'])
        ];
    }
}
