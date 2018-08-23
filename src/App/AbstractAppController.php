<?php

namespace tiFy\App;

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
     * @return void
     */
    public function __construct(AppInterface $app)
    {
        $this->app = $app;

        if (method_exists($this, 'boot')) :
            $this->boot();
        endif;
    }
}