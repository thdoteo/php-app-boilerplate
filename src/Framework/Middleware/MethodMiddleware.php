<?php

namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;

class MethodMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
        $parsedBody = $request->getParsedBody();
        if (array_key_exists('_METHOD', $parsedBody) &&
            in_array($parsedBody['_METHOD'], ['DELETE', 'PUT'])
        ) {
            $request = $request->withMethod($parsedBody['_METHOD']);
        }
        return $next($request);
    }
}
