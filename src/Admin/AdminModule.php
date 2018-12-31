<?php

namespace App\Admin;

use App\Admin\Actions\DashboardAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Renderer\TwigRenderer;
use Framework\Router;

class AdminModule extends Module
{

    const DEFINITIONS = __DIR__ . '/config.php';

    public function __construct(
        string $prefix,
        RendererInterface $renderer,
        Router $router,
        AdminTwigExtension $adminTwigExtension
    ) {
        $renderer->addPath('admin', __DIR__ . '/views');
        $router->get($prefix, DashboardAction::class, 'admin');

        if ($renderer instanceof TwigRenderer) {
            $renderer->getTwig()->addExtension($adminTwigExtension);
        }
    }
}
