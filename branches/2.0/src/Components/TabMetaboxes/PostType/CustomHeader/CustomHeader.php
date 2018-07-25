<?php

namespace tiFy\Components\TabMetaboxes\PostType\CustomHeader;

use tiFy\Metadata\Post as PostMetadata;
use tiFy\Field\Field;
use tiFy\TabMetabox\ContentPostTypeController;

class CustomHeader extends ContentPostTypeController
{
    /**
     * Chargement de la page d'administration courante de Wordpress.
     *
     * @param \WP_Screen $wp_screen Classe de rappel du controleur de la page d'administration courante de Wordpress.
     *
     * @return void
     */
    public function load($wp_screen)
    {
        $this->appServiceGet(PostMetadata::class)->register($this->getPostType(), '_custom_header', true);

        $this->appAddAction('admin_enqueue_scripts', function(){
            $this->appServiceGet(Field::class)->enqueue('MediaImage');
        });
    }

    /**
     * Affichage.
     *
     * @param \WP_Post $post Objet post Wordpress.
     * @param array $args Liste des variables passés en argument.
     *
     * @return string
     */
    public function display($post, $args)
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