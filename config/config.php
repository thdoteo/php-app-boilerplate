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
    RendererInterface::class => \DI\factory(TwigRendererFactory::class),
    \PDO::class => function (\Psr\Container\ContainerInterface $c) {
        $pdo = new PDO(
            'mysql:host=' . $c->get('database.host') . ';dbname=' . $c->get('database.name'),
            $c->get('database.user'),
            $c->get('database.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
        return $pdo;
    }
];