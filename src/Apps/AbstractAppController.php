<?php

namespace tiFy\Apps;

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
     * @return void
     */
    public function __construct(AppControllerInterface $app)
    {
        $this->app = $app;

        $this->boot();
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    abstract function boot();
}