<?php

/**
 * @name Metabox
 * @desc Personnalisation des boîtes de saisie.
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Metabox;

use Illuminate\Support\Collection;
use tiFy\Field\Field;
use tiFy\Metabox\MetaboxItemController;
use tiFy\Metabox\Tab\MetaboxTabDisplay;
use tiFy\Wp\WpScreen;

class Metabox
{
    /**
     * Liste des éléments.
     * @var MetaboxItemController[]
     */
    protected $items = [];

    /**
     * Liste des métaboxes à déclarer.
     * @var array
     */
    protected $registred = [];

    /**
     * Liste des métaboxes à supprimer.
     * @var array
     */
    protected $unregistred = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        add_action(
            'wp_loaded',
            function () {
                foreach (config('metabox.add', []) as $screen => $items) :
                    foreach ($items as $attrs) :
                        if (is_numeric($screen)) :
                            $_screen = isset($attrs['screen']) ? $attrs['screen'] : null;
                        else :
                            $_screen = $screen;
                        endif;

                        if(!is_null($_screen)) :
                            $this->items[] = app()->resolve(MetaboxItemController::class, [$_screen, $attrs]);
                        endif;
                    endforeach;
                endforeach;
            },
            0
        );

        add_action(
            'current_screen',
            function ($wp_current_screen) {
                $current_screen = new WpScreen($wp_current_screen);

                /** @var \WP_Screen  $wp_current_screen */
                foreach($this->items as $item) :
                    $item->load($current_screen);
                endforeach;

                app()->resolve(MetaboxTabDisplay::class, [$current_screen, $this]);
            },
            999999
        );

        add_action(
            'add_meta_boxes',
            function () {
                $this->removeHandle();
            },
            999999
        );
    }

    /**
     * Récupération de la liste des éléments.
     *
     * @return Collection
     */
    public function getItems()
    {
        return new Collection($this->items);
    }

    /**
     * Suppression de la liste des metaboxes déclarées
     *
     * @return void
     */
    private function removeHandle()
    {
        foreach ($this->unregistred as $post_type => $ids) :
            foreach ($ids as $id => $context) :
                remove_meta_box($id, $post_type, $context);

                // Hack Wordpress : Maintient du support de la modification du permalien
                if ($id === 'slugdiv') :
                    add_action(
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
        if (!isset($this->unregistred[$post_type])) :
            $this->unregistred[$post_type] = [];
        endif;

        $this->unregistred[$post_type][$id] = $context;
    }
}