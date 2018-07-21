<?php

namespace tiFy\App;

use tiFy\App\AppControllerInterface;

abstract class AbstractAppController
{
    /**
     * Classe de rappel du controleur de l'application.
     * @var AppControllerInterface
     */
    protected $app;

    /**
     * CONSTRUCTEUR.
     *
     * @param AppControllerInterface $app Classe de rappel du controleur de l'application.
     *
     * @return void
     */
    public function __construct(AppControllerInterface $app)
    {
        $this->app = $app;

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;
    }
}