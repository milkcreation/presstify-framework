<?php

namespace tiFy\Layout;

use tiFy\Contracts\Layout\LayoutAdminFactoryInterface;
use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Layout\LayoutMenuAdmin;

class LayoutAdminFactory extends AbstractLayoutFactory implements LayoutAdminFactoryInterface
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *
     *      @param string|callable $content Classe de rappel du controleur d'affichage.
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
     * }
     */
    protected $attributes = [];

    /**
     * Ecran courant d'affichage de la page.
     * @var null|\WP_Screen
     */
    protected $screen;

    /**
     * CONSTRUCTEUR.
     *
     * @param string $name Nom de qualification de la disposition associée.
     * @param array $attrs Attributs de configuration de la disposition associée.
     *
     * @return void
     */
    public function __construct($name, $attrs = [])
    {
        parent::__construct($name, $attrs);

        add_action(
            'admin_menu',
            function () {
                app('layout.admin.menu', [$this]);
            }
        );

        add_action(
            'current_screen',
            function ($wp_screen) {

                if ($wp_screen->id === $this->get('hookname')) :
                    $this->screen = $wp_screen;

                    if ($this->layout()) :
                        $this->load();
                    endif;

                    if ($this->layout() instanceof LayoutDisplayInterface) :
                        $this->layout()->load();
                    endif;
                endif;
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getScreen()
    {
        return $this->screen;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $this->getScreen()->add_option('per_page', ['option' => $this->layout()->param('per_page_option_name')]);
    }

    /**
     * {@inheritdoc}
     */
    public function isAdmin()
    {
        return true;
    }
}