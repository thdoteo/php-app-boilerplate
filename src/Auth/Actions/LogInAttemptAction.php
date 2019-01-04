<?php

namespace App\Auth\Actions;

use App\Auth\DatabaseAuth;
use Framework\Actions\RouterAwareAction;
use Framework\Response\RedirectResponse;
use Framework\Router;
use Framework\Session\FlashService;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface;

class LogInAttemptAction
{

    /**
     * @var DatabaseAuth
     */
    private $databaseAuth;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    use RouterAwareAction;

    /**
     * LogInAttemptAction constructor.
     *
     * @param DatabaseAuth $databaseAuth
     * @param Router $router
     * @param SessionInterface $session
     */
    public function __construct(
        DatabaseAuth $databaseAuth,
        Router $router,
        SessionInterface $session
    ) {
        $this->databaseAuth = $databaseAuth;
        $this->router = $router;
        $this->session = $session;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();
        $user = $this->databaseAuth->login($params['username'], $params['password']);
        if ($user) {
            $path = $this->session->get('auth.redirect') ?: $this->router->generateUri('admin');
            $this->session->delete('auth.redirect');
            return new RedirectResponse($path);
        }
        (new FlashService($this->session))->error('Invalid login or password.');
        return $this->redirect('auth.login');
    }
}
