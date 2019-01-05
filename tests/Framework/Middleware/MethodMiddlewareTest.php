<?php

namespace Tests\Framework\Middleware;

use Framework\Middleware\MethodMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class MethodMiddlewareTest extends TestCase
{
    /**
     * @var MethodMiddleware
     */
    private $middleware;

    /**
     * @var RequestHandlerInterface
     */
    private $handler;

    public function setUp()
    {
        $this->middleware = new MethodMiddleware();
        $this->handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
    }

    public function testAddMethod()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
            ->method('handle')
            ->with($this->callback(function ($request) {
                return $request->getMethod() === 'DELETE';
            }));

        $request = (new ServerRequest('POST', '/demo'))->withParsedBody([
            '_METHOD' => 'DELETE'
        ]);

        $this->middleware->process($request, $handler);
    }
}