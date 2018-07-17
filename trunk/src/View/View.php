<?php

namespace tiFy\View;

use tiFy\Apps\AppController;
use tiFy\Apps\Layout\LayoutViewInterface;

final class View extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appAddAction('init');
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        foreach($this->appConfig() as $name => $attrs) :
            $this->register($name, $attrs);
        endforeach;

        do_action('tify_view_register', $this);
    }

    /**
     * Déclaration d'un gabarit de l'interface d'administration.
     * 
     * @param string $name Nom de qualification du controleur
     * @param array $attrs {
     *      Attributs de configuration
     *
     *      @param string|callable $controller Classe de rappel du controleur d'affichage.
     *      @param array $params Liste des paramètres.
     * }
     *
     * @return LayoutViewInterface
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.view.{$name}";
        if($this->appServiceHas($alias)) :
            return;
        endif;

        if (empty($attrs['controller'])) :
            return;
        endif;

        $classname = $attrs['controller'];
        unset($attrs['controller']);

        $concrete = new $classname($name, $attrs, new ViewBaseController($alias));
        $this->appServiceShare($alias, $concrete);

        return $concrete;
    }
}