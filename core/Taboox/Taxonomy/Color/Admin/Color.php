<?php

namespace tiFy\Core\Taboox\Taxonomy\Color\Admin;

use tiFy\Core\Meta\Term as MetaTerm;
use tiFy\Core\Taboox\Taxonomy\Admin;
use tiFy\Core\Control\Control;

class Color extends Admin
{
    /**
     * Chargement de la page courante.
     *
     * @param \WP_Screen $current_screen
     *
     * @return void
     */
    public function current_screen($current_screen)
    {
        MetaTerm::set($current_screen->taxonomy, '_color', true);
    }

    /**
     * Mise en file des scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        \wp_enqueue_style('tify_control-colorpicker');
        \wp_enqueue_script('tify_control-colorpicker');
    }

    /**
     * Formulaire de saisie.
     *
     * @param \WP_Term $term Objet du terme courant Wordpress.
     * @param string $taxonomy Identifiant de qualification de la taxonomie associée au terme.
     *
     * @return void
     */
    public function form($term, $taxonomy)
    {
        echo Control::Colorpicker(
            [
                'name'    => 'tify_meta_term[_color]',
                'value'   => get_term_meta($term->term_id, '_color', true),
                'options' => [
                    'showInput' => true
                ]
            ]
        );
    }
}