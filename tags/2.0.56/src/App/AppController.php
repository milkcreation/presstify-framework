<?php

namespace tiFy\App;

use tiFy\Contracts\App\AppInterface;

abstract class AppController implements AppInterface
{
    use AppTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        if (did_action('tify_app_boot')) :
            $this->appBoot();
        else :
            add_action('tify_app_boot', [$this, 'appBoot']);
        endif;
    }

    /**
     * Initialisation du controleur d'application.
     * @internal Lancé à l'issue de l'initialisation complète.
     *
     * @return void
     */
    public function appBoot()
    {

    }
}