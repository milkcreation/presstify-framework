<?php

/**
 * @name Metabox
 * @desc Personnalisation des boîtes de saisie.
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\Metabox;

use Illuminate\Support\Collection;
use tiFy\Contracts\Metabox\MetaboxFactory;
use tiFy\Contracts\Metabox\MetaboxManager as MetaboxManagerContract;
use tiFy\Contracts\Wp\WpScreenInterface;
use tiFy\Wp\WpScreen;

class MetaboxManager implements MetaboxManagerContract
{
    /**
     * Liste des éléments déclarés.
     * @var MetaboxFactory[]
     */
    protected $items = [];

    /**
     * Instance de l'écran d'affichage courant.
     * @var WpScreenInterface
     */
    protected $screen;

    /**
     * Liste des éléments à supprimer.
     * @var array
     */
    protected $removes = [];

    /**
     * Liste des boîtes à onglets à personnaliser.
     * @var array
     */
    protected $tabs = [];

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
                foreach (config('metabox', []) as $screen => $items) :
                    if(!is_null($screen) && preg_match('#(.*)@(post_type|taxonomy|user)#', $screen)) :
                        $screen = 'edit::' . $screen;
                    endif;

                    foreach ($items as $name => $attrs) :
                        $this->items[] = app()->resolve('metabox.factory', [$name, $attrs, $screen]);
                    endforeach;
                endforeach;
            },
            0
        );

        add_action(
            'current_screen',
            function ($wp_current_screen) {
                $this->screen = app('wp.screen', [$wp_current_screen]);

                $attrs = [];
                foreach($this->tabs as $screen => $_attrs) :
                    if (preg_match('#(.*)@(post_type|taxonomy|user)#', $screen)) :
                        $screen = 'edit::' . $screen;
                    endif;
                    $WpScreen = WpScreen::get($screen);

                    if ($WpScreen->getHookname() === $this->screen->getHookname()) :
                        $attrs = $_attrs;
                        break;
                    endif;
                endforeach;

                /** @var \WP_Screen  $wp_current_screen */
                foreach($this->items as $item) :
                    $item->load($this->screen);
                endforeach;

                app('metabox.tab', [$attrs, $this->screen]);
            },
            999999
        );

        add_action(
            'add_meta_boxes',
            function () {
                foreach ($this->removes as $screen => $items) :
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
    public function add($name, $screen = null, $attrs = [])
    {
        if (empty($screen)) :
            $screen = '';
        endif;

        config()->set(
            "metabox.{$screen}.{$name}",
            $attrs
        );

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
    public function remove($id, $screen = null, $context = 'normal')
    {
        if (!$screen) :
            $screen = '';
        endif;

        if (!isset($this->removes[$screen])) :
            $this->removes[$screen] = [];
        endif;

        if (!isset($this->removes[$screen][$id])) :
            $this->removes[$screen][$id] = [];
        endif;

        array_push($this->removes[$screen][$id], $context);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function tab($attrs = [], $screen = null)
    {
        if (!$screen) :
            $screen = '';
        endif;

        $this->tabs[$screen] = $attrs;

        return $this;
    }
}