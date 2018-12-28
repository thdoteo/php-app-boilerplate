<?php

namespace Framework\Twig;

use Framework\Session\FlashService;

class FlashExtension extends \Twig_Extension
{
    /**
     * @var FlashService
     */
    private $flashService;

    public function __construct(FlashService $flashService)
    {
        $this->flashService = $flashService;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('flash', [$this, 'flash'])
        ];
    }

    public function flash(string $type): ?string
    {
        return $this->flashService->get($type);
    }
}
