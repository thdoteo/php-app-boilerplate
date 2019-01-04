<?php

namespace App\Auth\Actions;

use App\Auth\DatabaseAuth;
use Framework\Response\RedirectResponse;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;

class LogOutAction
{

    /**
     * @var DatabaseAuth
     */
    private $databaseAuth;

    /**
     * @var FlashService
     */
    private $flashService;

    public function __construct(DatabaseAuth $databaseAuth, FlashService $flashService)
    {
        $this->databaseAuth = $databaseAuth;
        $this->flashService = $flashService;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        $this->databaseAuth->logout();
        $this->flashService->success('You are now disconnected.');
        return new RedirectResponse('/');
    }
}
