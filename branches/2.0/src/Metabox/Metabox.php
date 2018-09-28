<?php

/**
 * @name Metabox
 * @desc Personnalisation des boîtes de saisie.
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Metabox;

use Illuminate\Support\Collection;
use tiFy\Contracts\Metabox\MetaboxInterface;
use tiFy\Contracts\Wp\WpScreenInterface;
use tiFy\Metabox\MetaboxItemController;
use tiFy\Metabox\Tab\MetaboxTabDisplay;
use tiFy\Wp\WpScreen;

class Metabox implements MetaboxInterface
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

                app(MetaboxTabDisplay::class, [$this->screen, $this]);
            },
            999999
        );

        add_action(
            'add_meta_boxes',
            function () {
                foreach (config('metabox.remove', []) as $screen => $items) :
                    if (preg_match('#(.*)@(post_type|taxonomy|user)#', $screen)) :
                        $screen = 'edit::' . $screen;
                    endif;
                    $WpScreen = WpScreen::get($screen);

                    foreach ($items as $id => $contexts) :
                        foreach($contexts as $context) :
                            remove_meta_box($id, $WpScreen->getObjectName(), $context);
                        endforeach;

                        // Hack Wordpress : Maintient du support de la modification du permalien.
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
            },
            999999
        );
    }

    /**
     * {@inheritdoc}
     */
    public function add($screen, $attrs = [])
    {
        config()->push("metabox.add.{$screen}", $attrs);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return new Collection($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($screen, $id, $context = 'normal')
    {
        config()->push("metabox.remove.{$screen}.{$id}", $context);

        return $this;
    }
}