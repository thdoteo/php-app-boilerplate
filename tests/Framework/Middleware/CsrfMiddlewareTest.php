<?php

namespace Tests\Framework\Middleware;

use Framework\Exception\CsrfInvalidException;
use Framework\Middleware\CsrfMiddleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddlewareTest extends TestCase
{
    /**
     * @var CsrfMiddleware
     */
    private $middleware;

    private $session;

    public function setUp()
    {
        $this->session = [];
        $this->middleware = new CsrfMiddleware($this->session);
    }

    public function testAllowGetRequest()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = (new ServerRequest('GET', '/demo'));

        $this->middleware->process($request, $handler);
    }

    public function testForbidPostRequestWithoutToken()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->never())
            ->method('handle')
            ->willReturn(new Response());

        $request = (new ServerRequest('POST', '/demo'));
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testAllowPostRequestWithToken()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = (new ServerRequest('POST', '/demo'));
        $token = $this->middleware->createToken();
        $request = $request->withParsedBody(['_CSRF' => $token]);
        $this->middleware->process($request, $handler);
    }

    public function testForbidPostRequestWithInvalidToken()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->never())
            ->method('handle')
            ->willReturn(new Response());

        $this->middleware->createToken();
        $request = (new ServerRequest('POST', '/demo'));
        $request = $request->withParsedBody(['_CSRF' => 'awww']);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testAllowPostRequestWithTokenOnce()
    {
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects($this->once())
            ->method('handle')
            ->willReturn(new Response());

        $request = (new ServerRequest('POST', '/demo'));
        $token = $this->middleware->createToken();
        $request = $request->withParsedBody(['_CSRF' => $token]);
        $this->middleware->process($request, $handler);
        $this->expectException(CsrfInvalidException::class);
        $this->middleware->process($request, $handler);
    }

    public function testLimitTheTokenNumber()
    {
        for ($i = 0; $i < 100; $i++) {
            $token = $this->middleware->createToken();
        }

        $this->assertCount(50, $this->session['CSRF']);
        $this->assertEquals($token, $this->session['CSRF'][49]);
    }
}