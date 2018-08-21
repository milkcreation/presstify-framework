<?php

namespace tiFy\App;

use tiFy\App\AppInterface;

abstract class AbstractAppController
{
    /**
     * Classe de rappel du controleur de l'application.
     * @var AppInterface
     */
    protected $app;

    /**
     * CONSTRUCTEUR.
     *
     * @param AppInterface $app Classe de rappel du controleur de l'application.
     *
     * @return void
     */
    public function __construct(AppInterface $app)
    {
        $this->app = $app;

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;
    }

    /**
     * Initialisation du controleur
     *
     * @return void
     */
    public function boot()
    {

    }
}