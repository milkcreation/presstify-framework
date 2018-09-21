<?php

namespace tiFy\PostType\Metabox\CustomHeader;

use tiFy\Metabox\AbstractMetaboxDisplayPostController;

class CustomHeader extends AbstractMetaboxDisplayPostController
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
    public function display($post, $args = [])
    {
        return field(
            'media-image',
            array_merge(
                [
                    'media_library_title' => __('Personnalisation de l\'image d\'entête', 'tify'),
                    'media_library_button' => __('Utiliser comme image d\'entête', 'tify'),
                    'name' => '_custom_header',
                    'value' => get_post_meta($post->ID, '_custom_header', true)
                ],
                $args
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function metadatas()
    {
        return [
            '_custom_header' => true
        ];
    }
}