<?php

/**
 * @name Metabox
 * @desc Personnalisation des boîtes de saisie.
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Metabox;

use Illuminate\Support\Collection;
use tiFy\Contracts\Wp\WpScreenInterface;
use tiFy\Metabox\MetaboxItemController;
use tiFy\Metabox\Tab\MetaboxTabDisplay;

class Metabox
{
    /**
     * Liste des éléments.
     * @var MetaboxItemController[]
     */
    protected $items = [];

    /**
     * Instance de l'écran d'affichage courant.
     * @var WpScreenInterface
     */
    protected $screen;

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
                            if (preg_match('#(.*)@(post_type|taxonomy|user)#', $_screen)) :
                                $_screen = 'edit::' . $_screen;
                            endif;

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
                $this->screen = app(WpScreenInterface::class, [$wp_current_screen]);

                /** @var \WP_Screen  $wp_current_screen */
                foreach($this->items as $item) :
                    $item->load($this->screen);
                endforeach;

                app()->resolve(MetaboxTabDisplay::class, [$this->screen, $this]);
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
     * Ajout d'un élément.
     *
     * @param string $screen Ecran d'affichage de l'élément.
     * @param array $attrs Liste des attributs de configuration de l'élément.
     *
     * @return $this
     */
    public function add($screen, $attrs = [])
    {
        config()->push("metabox.add.{$screen}", $attrs);

        return $this;
    }

    /**
     * Récupération de la liste des éléments.
     *
     * @return Collection|MetaboxItemController[]
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
        return;
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

                            echo field(
                                'hidden',
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