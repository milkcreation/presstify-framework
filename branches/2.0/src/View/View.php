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
     *      @param string|callable $controller Classe de rappel
     *      @param array $params Liste des paramètres.
     *      @param string $db Identifiant de base de données.
     *      @param string|array $labels Identifiant des intitulés.
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

        $this->appServiceShare($alias, new $classname($name, $attrs, new ViewBaseController($alias)));

        return $this->appServiceGet($alias);
    }
}