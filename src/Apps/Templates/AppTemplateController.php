<?php

namespace tiFy\Apps\Templates;

use tiFy\Apps\AppInterface;
use tiFy\Apps\Templates\AppEngine;
use tiFy\Kernel\Templates\TemplateController;

class AppTemplateController extends TemplateController implements AppTemplateInterface
{
    /**
     * Classe de rappel de l'application associée.
     * @var AppInterface
     */
    protected $app;

    /**
     * Instance of the template engine.
     * @var AppEngine
     */
    protected $engine;

    /**
     * CONSTRUCTEUR.
     *
     * @param Engine $engine
     * @param string $name
     * @param array $args Liste des variables passées en argument
     *
     * @return void
     */
    public function __construct(AppEngine $engine, $name, AppInterface $app)
    {
        $this->app = $app;

        parent::__construct($engine, $name);
    }
}