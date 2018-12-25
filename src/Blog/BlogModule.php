<?php

namespace App\Blog;

use Framework\Renderer;
use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class BlogModule
{
    /**
     * @var Renderer
     */
    private $renderer;

    /*
     * BlogModule constructor
     * @param Router $router
     */
    public function __construct(Router $router, Renderer $renderer)
    {
        // Init Renderer
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
