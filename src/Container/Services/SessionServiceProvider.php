<?php

declare(strict_types=1);

namespace tiFy\Container\Services;

use Pollen\Session\SessionManager;
use Pollen\Session\SessionManagerInterface;
use Pollen\Support\Env;
use tiFy\Container\ServiceProvider as BaseServiceProvider;

class SessionServiceProvider extends BaseServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [
        SessionManagerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(SessionManagerInterface::class, function () {
            $sessionManager = new SessionManager([], $this->getContainer());

            if ($tokenID = Env::get('APP_KEY')) {
                $sessionManager->setTokenID($tokenID);
            }

            return $sessionManager;
        });
    }
}