<?php

namespace App\Blog;

use Framework\Router;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class BlogModule
{
    /*
     * BlogModule constructor
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $router->get('/blog', [$this, 'index'], 'blog.index');
        $router->get('/blog/{slug:[a-z\-]+}', [$this, 'show'], 'blog.show');
    }

    public function index(Request $request): string
    {
        return 'welcome on the blog';
    }

    public function show(Request $request): string
    {
        return 'show ' . $request->getAttribute('slug');
    }
}
