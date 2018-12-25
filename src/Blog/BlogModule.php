<?php

namespace App\Blog;

use Framework\Renderer\RendererInterface;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;

class BlogModule
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /*
     * BlogModule constructor
     * @param Router $router
     */
    public function __construct(Router $router, RendererInterface $renderer)
    {
        // Init PHPRenderer
        $this->renderer = $renderer;
        $this->renderer->addPath('blog', __DIR__ . '/views');

        // Init Router
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug:[a-z\-]+}', [$this, 'show'], 'blog.show');
    }

    public function index(Request $request): string
    {
        return $this->renderer->render('@blog/index');
    }

    public function show(Request $request): string
    {
        return $this->renderer->render('@blog/show', [
            'slug' => $request->getAttribute('slug')
        ]);
    }
}
