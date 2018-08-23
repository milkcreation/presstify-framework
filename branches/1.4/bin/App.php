<?php

namespace tiFy;

use tiFy\Contracts\App\AppInterface;
use tiFy\App\Traits\App as TraitsApp;

abstract class App implements AppInterface
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

        if (!did_action('tify_app_boot')) :
            add_action('tify_app_boot', [$this, 'appBoot']);
        endif;
    }
}