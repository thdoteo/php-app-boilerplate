<?php

namespace Framework\Middleware;

use Framework\Exception\CsrfInvalidException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CsrfMiddleware implements MiddlewareInterface
{

    /**
     * @var mixed
     */
    private $session;

    /**
     * @var string
     */
    private $formKey;

    /**
     * @var string
     */
    private $sessionKey;

    /**
     * @var int
     */
    private $limit;

    /**
     * CsrfMiddleware constructor.
     * @param mixed $session
     * @param int $limit
     * @param string $formKey
     * @param string $sessionKey
     */
    public function __construct(&$session, int $limit = 50, string $formKey = '_CSRF', string $sessionKey = 'CSRF')
    {
        $this->validSession($session);
        $this->session = &$session;
        $this->formKey = $formKey;
        $this->sessionKey = $sessionKey;
        $this->limit = $limit;
    }

    /**
     * Process an incoming server request.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $params = $request->getParsedBody() ?: [];
            if (!array_key_exists($this->formKey, $params)) {
                $this->rejectRequest();
            } else {
                $csrfList = $this->session[$this->sessionKey] ?? [];
                if (in_array($params[$this->formKey], $csrfList)) {
                    $this->removeToken($params[$this->formKey]);
                    return $handler->handle($request);
                } else {
                    $this->rejectRequest();
                }
            }
        }
        return $handler->handle($request);
    }

    /**
     * Generates a CSRF token and adds it to the session.
     * @throws \Exception
     */
    public function createToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $csrfList = $this->session[$this->sessionKey] ?? [];
        $csrfList[] = $token;

        $this->session[$this->sessionKey] = $csrfList;
        $this->limitTokens();
        return $token;
    }

    /**
     * Removes a token from the list of tokens in the session.
     * @param $token
     */
    private function removeToken($token): void
    {
        $tokens = array_filter($this->session[$this->sessionKey], function ($t) use ($token) {
            return $token !== $t;
        });
        $this->session[$this->sessionKey] = $tokens;
    }

    /**
     * Rejects a request.
     * @throws \Exception
     */
    private function rejectRequest(): void
    {
        throw new CsrfInvalidException();
    }

    private function limitTokens()
    {
        $tokens = $this->session[$this->sessionKey] ?? [];
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        $this->session[$this->sessionKey] = $tokens;
    }

    private function validSession($session)
    {
        if (!is_array($session) && !$session instanceof \ArrayAccess) {
            throw new \TypeError('Session must be handleable as an array.');
        }
    }

    /**
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->formKey;
    }
}
