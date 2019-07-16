<?php

namespace tiFy\Core\Security;

use tiFy\App\Traits\App as TraitsApp;

class Security
{
    use TraitsApp;

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        new LoginRedirect;
    }
}