<?php

namespace App\Blog;

use App\Blog\Actions\CategoryCrudAction;
use App\Blog\Actions\PostCrudAction;
use App\Blog\Actions\PostAction;
use Framework\Module;
use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Container\ContainerInterface;

class BlogModule extends Module
{
    const DEFINITIONS = __DIR__ . '/config.php';

    const MIGRATIONS = __DIR__ . '/db/migrations';
    const SEEDS = __DIR__ . '/db/seeds';

    /*
     * BlogModule constructor
     * @param Router $router
     */
    public function __construct(ContainerInterface $container)
    {
        $container->get(RendererInterface::class)->addPath('blog', __DIR__ . '/views');

        $router = $container->get(Router::class);
        $prefix = $container->get('blog.prefix');

        $router->get($prefix, PostAction::class, 'blog.index');
        $router->get($prefix . '/{slug:[a-z\-]+}-{id:[0-9]+}', PostAction::class, 'blog.show');

        if ($container->has('admin.prefix')) {
            $prefixAdmin = $container->get('admin.prefix');
            $router->crud($prefixAdmin . '/posts', PostCrudAction::class, 'blog.admin');
            $router->crud($prefixAdmin . '/categories', CategoryCrudAction::class, 'blog.admin.categories');
        }
    }
}
