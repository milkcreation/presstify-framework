<?php

namespace tiFy\Taxonomy\Metabox\CustomHeader;

use tiFy\Metabox\MetaboxWpTermController;

class CustomHeader extends MetaboxWpTermController
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
    public function content($term = null, $taxonomy = null, $args = null)
    {
        return field(
            'media-image',
            array_merge(
                [
                    'media_library_title' => __('Personnalisation de l\'image d\'entête', 'tify'),
                    'media_library_button' => __('Utiliser comme image d\'entête', 'tify'),
                    'name' => '_custom_header',
                    'value' => get_term_meta($term->term_id, '_custom_header', true)
                ],
                $this->all()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function header($term = null, $taxonomy = null, $args = null)
    {
        return $this->item->getTitle() ? : __('Image d\'entête', 'tify');
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