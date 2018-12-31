<?php

namespace Framework\Router;

use Framework\Router;

class RouterTwigExtension extends \Twig_Extension
{
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('path', [$this, 'path']),
            new \Twig_SimpleFunction('is_active_route', [$this, 'isActiveRoute'])
        ];
    }

    /**
     * @param string $path
     * @param array $params
     * @return string
     */
    public function path(string $path, array $params = []): string
    {
        return $this->router->generateUri($path, $params);
    }

    /**
     * @param string $route
     * @return bool
     */
    public function isActiveRoute(string $route): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $expectedUri = $this->router->generateUri($route);
        return strpos($uri, $expectedUri) !== false;
    }
}
