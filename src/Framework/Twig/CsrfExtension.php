<?php

namespace Framework\Twig;

use Framework\Middleware\CsrfMiddleware;

class CsrfExtension extends \Twig_Extension
{

    /**
     * @var CsrfMiddleware
     */
    private $csrfMiddleware;

    public function __construct(CsrfMiddleware $csrfMiddleware)
    {
        $this->csrfMiddleware = $csrfMiddleware;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('csrf', [$this, 'csrf'], ['is_safe' => ['html']])
        ];
    }

    public function csrf(): string
    {
        return '<input type="hidden" ' .
            'name="' . $this->csrfMiddleware->getFormKey() .
            '" value="' . $this->csrfMiddleware->createToken() . '">';
    }
}
