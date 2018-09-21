<?php

namespace tiFy\Options\Metabox\CustomHeader;

use tiFy\Metabox\AbstractMetaboxDisplayOptionsController;

class CustomHeader extends AbstractMetaboxDisplayOptionsController
{
    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        add_action(
            'admin_enqueue_scripts',
            function(){
                field('media-image')->enqueue_scripts();
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function display($args = [])
    {
        return field(
            'media-image',
            array_merge(
                [
                    'media_library_title'  => __('Personnalisation de l\'image d\'entête', 'tify'),
                    'media_library_button' => __('Utiliser comme image d\'entête', 'tify'),
                    'name'                 => 'custom_header',
                    'value'                => get_option('custom_header')
                ],
                $args
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function settings()
    {
        return ['custom_header'];
    }
}