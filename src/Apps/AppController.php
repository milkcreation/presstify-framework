<?php

namespace tiFy\Apps;


abstract class AppController
{
    use AppTrait;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        if (! $this->appExists($this)) :
            $this->appRegister($this);
        endif;

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