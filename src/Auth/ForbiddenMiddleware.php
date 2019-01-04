<?php

namespace App\Auth;

use Framework\Auth\ForbiddenException;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ForbiddenMiddleware implements MiddlewareInterface
{

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * Process an incoming server request.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ForbiddenException $exception) {
            $this->session->set('auth.redirect', $request->getUri()->getPath());
            (new FlashService($this->session))->error('You are not allowed to access this page.');
            $loginPath = $this->router->generateUri('auth.login');
            return new RedirectResponse($loginPath);
        }
    }
}
