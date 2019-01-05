<?php

namespace Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MethodMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parsedBody = $request->getParsedBody();
        if (array_key_exists('_METHOD', $parsedBody) &&
            in_array($parsedBody['_METHOD'], ['DELETE', 'PUT'])
        ) {
            $request = $request->withMethod($parsedBody['_METHOD']);
        }
        return $handler->handle($request);
    }
}
