<?php

namespace tiFy\Taxonomy\Metabox\Color;

use tiFy\Metabox\AbstractMetaboxDisplayTermController;

class Color extends AbstractMetaboxDisplayTermController
{
    /**
     * {@inheritdoc}
     */
    public function content($term = null, $taxonomy = null, $args = null)
    {
        return field(
            'colorpicker',
            [
                'name'    => '_color',
                'value'   => get_term_meta($term->term_id, '_color', true),
                'options' => [
                    'showInput' => true
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function header($term = null, $taxonomy = null, $args = null)
    {
        return $this->item->getTitle() ?: __('Couleur', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        add_action(
            'admin_enqueue_scripts',
            function () {
                field('colorpicker')->enqueue_scripts();
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function metadatas()
    {
        return ['_color' => true];
    }
}