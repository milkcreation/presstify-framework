<?php

namespace tiFy\App\Container;

use tiFy\Contracts\App\AppInterface;
use tiFy\Kernel\Container\ServiceProvider;
use tiFy\Kernel\Container\ServiceProviderInterface;
use tiFy\tiFy;

class AppServiceProvider extends ServiceProvider implements ServiceProviderInterface
{
    /**
     * Classe de rappel du controleur de l'interface associée.
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

        parent::__construct(tiFy::instance());
    }

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return $this->app;
    }
}