<?php

namespace Framework;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Framework;

class App
{
    /**
     * App's container
     * @var ContainerInterface
     */
    private $container;

    /**
     * List of modules
     * @var array
     */
    private $modules = [];

    /**
     * App constructor.
     * @param ContainerInterface $container
     * @param string[] $modules
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;
        foreach ($modules as $module) {
            $this->modules[] = $container->get($module);
        }
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri()->getPath();

        $parsedBody = $request->getParsedBody();
        if (array_key_exists('_METHOD', $parsedBody) &&
            in_array($parsedBody['_METHOD'], ['DELETE', 'PUT'])
        ) {
            $request = $request->withMethod($parsedBody['_METHOD']);
        }

        // Handle URLs ending with /
        if (!empty($uri) && $uri[-1] === "/") {
            return (new Response())
                ->withStatus(301)
                ->withHeader('Location', substr($uri, 0, -1));
        }

        // Handle route
        $router = $this->container->get(Router::class);
        $route = $router->match($request);
        if (is_null($route)) {
            return new Response(404, [], 'error 404');
        }

        // Prepare params
        $params = $route->getParams();
        $request = array_reduce(array_keys($params), function ($request, $key) use ($params) {
            return $request->withAttribute($key, $params[$key]);
        }, $request);

        // Call the route's callback
        $callback = $route->getCallback();
        if (is_string($callback)) {
            $callback = $this->container->get($callback);
        }
        $response = call_user_func_array($callback, [$request]);

        // Handle response
        if (is_string($response)) {
            return new Response(200, [], (string)$response);
        } elseif ($response instanceof ResponseInterface) {
            return $response;
        } else {
            throw new \Exception('Response is not a string or an instance of ResponseInterface.');
        }
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}
