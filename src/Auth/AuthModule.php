<?php

namespace App\Auth;

use App\Auth\Actions\LogInAction;
use App\Auth\Actions\LogInAttemptAction;
use App\Auth\Actions\LogOutAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class AuthModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS = __DIR__ . '/db/migrations';

    const SEEDS = __DIR__ . '/db/seeds';

    public function __construct(ContainerInterface $container, Router $router, RendererInterface $renderer)
    {
        $renderer->addPath('auth', __DIR__ . '/views');

        $prefix = $container->get('auth.prefix');
        $router->get($prefix . '/login', LogInAction::class, 'auth.login');
        $router->post($prefix . '/login', LogInAttemptAction::class);
        $router->post($prefix . '/logout', LogOutAction::class, 'auth.logout');
    }
}
