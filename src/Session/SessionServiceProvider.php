<?php

declare(strict_types=1);

namespace tiFy\Session;

use Pollen\Session\SessionManagerInterface;
use Pollen\Support\Env;
use tiFy\Container\ServiceProvider;

class SessionServiceProvider extends ServiceProvider
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