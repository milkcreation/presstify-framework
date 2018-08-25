<?php

namespace tiFy\Components\TabMetaboxes\PostType\ImageGallery;

use tiFy\Metadata\Post as PostMetadata;
use tiFy\TabMetabox\ContentPostTypeController;

class ImageGallery extends ContentPostTypeController
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->appAddAction('wp_ajax_tify_tab_metabox_post_type_image_gallery_add_item', [$this, 'wp_ajax']);
        $this->appTemplateMacro('displayItem', [$this, 'displayItem']);
    }

    /**
     * Mise en file des scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        wp_enqueue_media();
        wp_enqueue_style(
            'tiFyTabMetaboxPostTypeImageGallery',
            $this->appAssetUrl('/TabMetaboxes/PostType/ImageGallery/css/styles.css'),
            ['tiFyAdmin'],
            180808
        );
        wp_enqueue_script(
            'tiFyTabMetaboxPostTypeImageGallery',
            $this->appAssetUrl('/TabMetaboxes/PostType/ImageGallery/js/scripts.js'),
            ['jquery', 'jquery-ui-sortable'],
            180808,
            true
        );
        wp_localize_script(
            'tiFyTabMetaboxPostTypeImageGallery',
            'tify_taboox_image_gallery',
            [
                'maxAttempt' => __('Nombre maximum d\'images dans la galerie atteint', 'tify'),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'name' => '_tify_taboox_image_gallery',
            'max'  => -1,
            'templates' => [
                'controller' => TemplateController::class
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display($post, $args = [])
    {
        $this->set('items', get_post_meta($post->ID, $this->get('name')) ? : []);

        return $this->appTemplateRender('display', $this->all());
    }

    /**
     * Affichage d'un élément
     *
     * @param int $id Identifiant de qualification de l'élément.
     * @param int $order Ordre d'affichage de l'élément.
     * @param string string $name Nom d'enregistrement de l'élément.
     *
     * @return string
     */
    public function displayItem($id, $order, $name)
    {
        // Bypass
        if (!$image = wp_get_attachment_image_src($id, 'thumbnail')) :
            return;
        endif;

        $item['id'] = $id;
        $item['src'] = $image[0];
        $item['name'] = "{$name}[]";
        $item['order'] = $order;

        return $this->appTemplateRender('item', $item);
    }

    /**
     * {@inheritdoc}
     */
    public function load($current_screen)
    {
        $this->appAddAction('admin_enqueue_scripts');
    }

    /**
     * {@inheritdoc}
     */
    public function metadatas()
    {
        return [
            $this->get('name') => true
        ];
    }

    /**
     * Action Ajax
     *
     * @return string
     */
    public function wp_ajax()
    {
        echo $this->displayItem(
            $this->appRequest('POST')->get('id'),
            $this->appRequest('POST')->get('order'),
            $this->appRequest('POST')->get('name')
        );

        exit;
    }
}