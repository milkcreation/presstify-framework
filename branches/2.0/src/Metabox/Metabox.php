<?php

/**
 * @name Metabox
 * @desc Personnalisation des boîtes de saisie
 * @package presstiFy
 * @subpackage Core
 * @namespace tiFy\Metabox
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 * @version 1.2.580
 */

namespace tiFy\Metabox;

use tiFy\Apps\AppController;
use tiFy\Field\Field;

class Metabox extends AppController
{
    /**
     * Liste des métaboxes à supprimer.
     * @var array
     */
    protected $removed = [];

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $this->appAddAction('add_meta_boxes', null, 99);
    }

    /**
     * Appel à l'issue des déclarations complète des métaboxes natives Wordpress.
     *
     * @return void
     */
    final public function add_meta_boxes()
    {
        do_action('tify_metabox_register');

        // Suppression des metaboxes
        $this->removeHandle();
    }

    /**
     * Suppression de la liste des metaboxes déclarées
     *
     * @return void
     */
    private function removeHandle()
    {
        if (! $this->removed) :
            return;
        endif;

        foreach ($this->removed as $post_type => $ids) :
            foreach ($ids as $id => $context) :
                remove_meta_box($id, $post_type, $context);

                // Hack Wordpress : Maintient du support de la modification du permalien
                if ($id === 'slugdiv') :
                    $this->appAddAction(
                        'edit_form_before_permalink',
                        function($post) use ($post_type) {
                            if($post->post_type !== $post_type) :
                                return;
                            endif;

                            $editable_slug = apply_filters('editable_slug', $post->post_name, $post);

                            echo Field::Hidden(
                                [
                                    'name'  => 'post_name',
                                    'value' => esc_attr($editable_slug),
                                    'attrs' => [
                                        'id' => 'post_name',
                                        'autocomplete' => 'off'
                                    ]
                                ]
                            );
                        }
                    );
                endif;
            endforeach;
        endforeach;
    }

    /**
     * Déclaration d'une boîte de sasie à supprimer
     *
     * @param string $id Identifiant de qualification de la metaboxe
     * @param string $post_type Identifiant de qualification du type de post
     * @param string $context normal|side|advanced
     *
     * @return void
     */
    public function remove($id, $post_type, $context = 'normal')
    {
        if (did_action('add_meta_boxes_' . $post_type)) :
            trigger_error(__('Pour être fonctionnelle, la déclaration de suppression de boîte de saisie devrait être faite avant l\'execution de l\'action "add_meta_boxes". Vous pourriez utiliser l\'action "tify_metabox_register" pour y appeler vos déclarations.', 'tify'));
        endif;

        if (! isset($this->removed[$post_type])) :
            $this->removed[$post_type] = [];
        endif;

        $this->removed[$post_type][$id] = $context;
    }
}