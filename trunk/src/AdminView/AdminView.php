<?php

namespace tiFy\AdminView;

use tiFy\Apps\AppController;

final class AdminView extends AppController
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

        do_action('tify_admin_view_register', $this);
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
     *      @param bool|array $admin_menu {
     *          Attributs de configuration du menu d'administration (false: désactiver l'affichage)
     *
     *          @param string $menu_slug Identifiant du menu - Identifiant du template par défaut
     *          @param string $parent_slug Identifiant du menu parent pour les sous-menus uniquement.
     *          @param string $page_title Intitulé de la page
     *          @param string $menu_title Intitulé du menu - Intitulé du modèle prédéfini si vide
     *          @param string $capability Habiltation d'affichage
     *          @param string $icon_url Icone de menu (hors sous-menu : 'parent_slug' => null)
     *          @param int $position Ordre d'affichage de l'entrée de menu
     *          @param string Fonction d'affichage de la page - Factory::render() par défaut
     *      }
     *      @param array handle Tableau associatif de la liste des templates en relation
     * }
     *
     * @return AdminViewControllerInterface
     */
    public function register($name, $attrs = [])
    {
        $alias = "tfy.admin_view.{$name}";
        if($this->appServiceHas($alias)) :
            return;
        endif;

        $classname = !empty($attrs['controller']) ? $attrs['controller'] : AdminViewBaseController::class;
        unset($attrs['controller']);

        $this->appServiceShare($alias, new $classname($name, $attrs));

        return $this->appServiceGet($alias);
    }
}