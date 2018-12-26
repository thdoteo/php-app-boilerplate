<?php

use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRendererFactory;
use Framework\Router;

return [
    'database.host' => 'localhost',
    'database.user' => 'root',
    'database.password' => 'root',
    'database.name' => 'phpframework',
    'views.path' => dirname(__DIR__) . '/views',
    'twig.extensions' => [
        \DI\get(Router\RouterTwigExtension::class)
    ],
    Router::class => \DI\create(),
    RendererInterface::class => \DI\factory(TwigRendererFactory::class)
];