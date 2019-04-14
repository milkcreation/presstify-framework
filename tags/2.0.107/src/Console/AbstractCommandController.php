<?php

namespace tiFy\Console;

abstract class AbstractCommandController
{
    /**
     * Instance de l'application du controleur.
     *
     * @var ControllerApplication
     */
    protected $app;

    /**
     * AbstractController constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setApp();
    }

    /**
     * Définition de l'instance de l'application du controleur.
     *
     * @return void
     */
    public function setApp()
    {
        $this->app = app()->get('console.controller.application');
    }
}