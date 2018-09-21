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

    /**
     * Récupération d'une instance de l'application ou d'un service fourni par celle-ci.
     * {@internal Si $abstract est null > Retourne l'instance de l'appication.}
     * {@internal Si $abstract est qualifié > Retourne la résolution du service qualifié.}
     *
     * @param null|string $abstract Nom de qualification du service.
     * @param array $args Liste des variables passé en arguments lors de la résolution du service.
     *
     * @return object|AppInterface|AppContainer
     */
    public function app($abstract = null, $args = [])
    {
        if (is_null($abstract)) :
            return $this->app;
        endif;

        return $this->app->resolve($abstract, $args);
    }
}