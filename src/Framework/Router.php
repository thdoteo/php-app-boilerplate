<?php

namespace Framework;

use Framework\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\Route as ZendRoute;

/**
 * Class Router
 * Represents the router
 */
class Router
{

    /**
     * @var FastRouteRouter
     */
    private $router;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }

    /**
     * Registers a route in GET
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function get(string $path, $callable, ?string $name = null)
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['GET'], $name));
    }

    /**
     * Registers a route in POST
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function post(string $path, $callable, ?string $name = null)
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['POST'], $name));
    }

    /**
     * Registers a route in DELETE
     * @param string $path
     * @param string|callable $callable
     * @param string $name
     */
    public function delete(string $path, $callable, ?string $name = null)
    {
        $this->router->addRoute(new ZendRoute($path, $callable, ['DELETE'], $name));
    }

    /**
     * Generates routes for crud
     * @param string $prefixPath
     * @param $callable
     * @param $prefixName
     */
    public function crud(string $prefixPath, $callable, $prefixName)
    {
        $this->get($prefixPath, $callable, 'blog.admin.index');

        $this->get($prefixPath . '/create', $callable, $prefixName . '.create');
        $this->post($prefixPath . '/create', $callable);

        $this->get($prefixPath . '/{id:[0-9]+}', $callable, $prefixName . '.edit');
        $this->post($prefixPath . '/{id:[0-9]+}', $callable);

        $this->delete($prefixPath . '/{id:[0-9]+}', $callable, $prefixName . '.delete');
    }


    /**
     * Matches a request with a registered route
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result->getMatchedMiddleware(),
                $result->getMatchedParams()
            );
        }
        return null;
    }

    /**
     * Generates an URI
     * @param string $name
     * @param array $params
     * @param array $queryParams
     * @return string|null
     */
    public function generateUri(string $name, array $params = [], array $queryParams = []): ?string
    {
        $uri = $this->router->generateUri($name, $params);
        if (!empty($queryParams)) {
            return $uri . '?' . http_build_query($queryParams);
        }
        return $uri;
    }
}
