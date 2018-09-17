<?php

namespace tiFy\Components\TabMetaboxes\Options\CustomHeader;

use tiFy\Field\Field;
use tiFy\TabMetabox\ContentOptionsController;

class CustomHeader extends ContentOptionsController
{
    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        $this->appAddAction('admin_enqueue_scripts', function(){
            $this->appServiceGet(Field::class)->enqueue('MediaImage');
        });
    }

    /**
     * {@inheritdoc}
     */
    public function display($args = [])
    {
        return Field::MediaImage(
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