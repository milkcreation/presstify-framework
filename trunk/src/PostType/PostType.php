<?php

namespace tiFy\PostType;

use tiFy\Apps\AppController;

final class PostType extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $this->appAddAction('init', null, 0);
    }

    /**
     * Déclaration des types de posts personnalisés.
     *
     * @return void
     */
    public function init()
    {
        if (!$post_types = $this->appConfig(null, [])) :
            foreach ($post_types as $name => $args) :
                $this->register($name, $args);
            endforeach;
        endif;

        do_action('tify_post_type_register', $this);
    }

    /**
     * Création du type de post personnalisé
     *
     * @param string $name Nom de qualification du type de post.
     * @param array $attrs Liste des attributs de configuration
     *
     * @return void
     */
    public function register($name, $attrs = [])
    {
        $attrs = $this->parseAttrs($name, $attrs);

        $allowed = [
            'label',
            'labels',
            'description',
            'public',
            'exclude_from_search',
            'publicly_queryable',
            'show_ui',
            'show_in_nav_menus',
            'show_in_menu',
            'show_in_admin_bar',
            'menu_position',
            'menu_icon',
            'capability_type',
            'map_meta_cap',
            'hierarchical',
            'supports',
            'register_meta_box_cb',
            'has_archive',
            'permalink_epmask',
            'rewrite',
            'query_var',
            'can_export',
            'show_in_rest',
            'rest_base',
            'rest_controller_class',
        ];

        $_attrs = [];
        foreach ($allowed as $key) :
            if (isset($attrs[$key])) :
                $_attrs[$key] = $attrs[$key];
            endif;
        endforeach;


    }

    /**
     * Traitement des arguments par défaut de type de post personnalisé
     * @see https://codex.wordpress.org/Function_Reference/register_post_type
     *
     * @param string $taxonomy Identifiant de qualification de la taxonomie
     * @param array $attrs Liste des attributs de configuration personnalisés
     *
     * @return array
     */
    private function parseAttrs($post_type, $args = [])
    {

    }
}