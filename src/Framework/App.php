<?php

namespace Framework;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App implements RequestHandlerInterface
{

    /**
     * List of modules
     * @var array
     */
    private $modules = [];

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $configPath;

    /**
     * @var string[]
     */
    private $middlewares;

    /**
     * Current middleware index
     * @var int
     */
    private $middlewareIndex = 0;

    public function __construct(string $configPath)
    {
        $this->configPath = $configPath;
    }

    /**
     * Adds a module to the app
     * @param string $module
     * @return App
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Adds a middleware to the app
     * @param string $middleware
     * @return App
     */
    public function pipe(string $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->getMiddleware();
        if (is_null($middleware)) {
            throw new \Exception('No middleware has handled this request.');
        } elseif (is_callable($middleware)) {
            return call_user_func_array($middleware, [$request, [$this, 'handle']]);
        } elseif ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $this);
        }
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // Init modules
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }

        return $this->handle($request);
    }

    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();
            $env = getenv('ENV') ?: 'production';
            if ($env === 'production') {
                $builder->enableCompilation('tmp');
                $builder->writeProxiesToFile(true, 'tmp/proxies');
            }
            $builder->addDefinitions($this->configPath);

            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }

            $this->container = $builder->build();
        }
        return $this->container;
    }

    private function getMiddleware()
    {
        if (array_key_exists($this->middlewareIndex, $this->middlewares)) {
            $middleware = $this->container->get($this->middlewares[$this->middlewareIndex]);
            $this->middlewareIndex++;
            return $middleware;
        }
        return null;
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }
}
