<?php

namespace tiFy;

use tiFy\App\AppControllerInterface;
use tiFy\App\Traits\App as TraitsApp;

abstract class App implements AppControllerInterface
{
    use TraitsApp;

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        $this->tFyAppOnInit();
        $this->appBoot();
    }
}