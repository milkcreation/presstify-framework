<?php

namespace tiFy\Components\TabMetaboxes\PostType\CustomHeader;

use tiFy\Metadata\Post as PostMetadata;
use tiFy\Field\Field;
use tiFy\TabMetabox\ContentPostTypeController;

class CustomHeader extends ContentPostTypeController
{
    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        $this->appServiceGet(PostMetadata::class)->register($this->getPostType(), '_custom_header', true);

        $this->appAddAction('admin_enqueue_scripts', function(){
            $this->appServiceGet(Field::class)->enqueue('MediaImage');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function display($post, $args = [])
    {
        return Field::MediaImage(
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
}