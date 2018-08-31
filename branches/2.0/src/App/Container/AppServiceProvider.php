<?php

namespace tiFy\App\Container;

use tiFy\Contracts\App\AppInterface;
use tiFy\Contracts\Container\ContainerInterface;
use tiFy\Contracts\Container\ServiceProviderInterface;
use tiFy\Kernel\Container\ServiceProvider;
use tiFy\tiFy;

class AppServiceProvider extends ServiceProvider implements ServiceProviderInterface
{
    /**
     * Classe de rappel du controleur de l'interface associée.
     * @var AppInterface|ContainerInterface
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