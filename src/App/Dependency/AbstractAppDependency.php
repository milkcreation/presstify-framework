<?php

namespace tiFy\App\Dependency;

use tiFy\Contracts\App\AppInterface;
use tiFy\Contracts\Container\ContainerInterface;

abstract class AbstractAppDependency
{
    /**
     * Classe de rappel du controleur de l'application.
     * @var AppInterface|ContainerInterface
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
            if (!did_action('tify_app_boot')) :
                add_action('tify_app_boot', [$this, 'boot']);
            else :
                $this->boot();
            endif;
        endif;
    }
}