<?php

namespace Framework\Router;

/**
 * Class Route
 * Represents a matched route
 */
class Route
{

    private $name;
    private $callable;
    private $parameters;

    /**
     * Route constructor.
     * @param string $name
     * @param string|callable $callable
     * @param array $parameters
     */
    public function __construct(string $name, $callable, array $parameters)
    {
        $this->name = $name;
        $this->callable = $callable;
        $this->parameters = $parameters;
    }

    /**
     * Returns the route's name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the route's callback
     * @return string|callable
     */
    public function getCallback()
    {
        return $this->callable;
    }

    /**
     * Retrieves the URL parameters
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
