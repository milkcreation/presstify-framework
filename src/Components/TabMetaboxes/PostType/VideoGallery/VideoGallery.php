<?php

namespace tiFy\Components\TabMetaboxes\PostType\VideoGallery;

use tiFy\Field\Field;
use tiFy\Metadata\Post as PostMetadata;
use tiFy\TabMetabox\ContentPostTypeController;

class VideoGallery extends ContentPostTypeController
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->appAddAction('wp_ajax_tify_tab_metabox_post_type_video_gallery_add_item', [$this, 'wp_ajax']);
        $this->appTemplateMacro('displayItem', [$this, 'displayItem']);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'name' => '_tify_taboox_video_gallery',
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
        /** @var PostMetadata $postMetadata */
        $postMetadata = $this->appServiceGet(PostMetadata::class);
        $this->set('items', $postMetadata->get($post->ID, $this->get('name')) ? : []);

        return $this->appTemplateRender('display', $this->all());
    }

    /**
     * Affichage d'un élément
     *
     * @param int $id Identifiant de qualification de l'élément.
     * @param array $attrs Attributs de configuration de l'élément.
     * @param string string $name Nom d'enregistrement de l'élément.
     *
     * @return string
     */
    public function displayItem($id, $attrs, $name)
    {
        $attrs = array_merge(
            [
                'poster' => '',
                'src' => ''
            ],
            $attrs
        );
        $attrs['poster_src'] =
            ($attrs['poster'] && ($image = \wp_get_attachment_image_src($attrs['poster'], 'thumbnail')))
                ? $image[0]
                : '';
        $attrs['name'] = $name;
        $attrs['id'] = $id;

        return $this->appTemplateRender('item', $attrs);
    }

    /**
     * {@inheritdoc}
     */
    public function load($current_screen)
    {
        $this->appAddAction(
            'admin_enqueue_scripts',
            function () {
                wp_enqueue_media();

                wp_enqueue_style(
                    'MetaboxPostTypeVideoGallery',
                    \assets()->url('/metabox/post-type/video-gallery/css/styles.css'),
                    ['tiFyAdmin'],
                    180724
                );

                wp_enqueue_script(
                    'MetaboxPostTypeVideoGallery',
                    \assets()->url('/metabox/post-type/video-gallery/js/scripts.js'),
                    ['jquery', 'jquery-ui-sortable', 'tiFyAdmin'],
                    180724,
                    true
                );
                wp_localize_script(
                    'MetaboxPostTypeVideoGallery',
                    'tify_taboox_video_gallery',
                    [
                        'maxAttempt' => __('Nombre maximum de vidéos dans la galerie atteint', 'tify'),
                    ]
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function metadatas()
    {
        return [
            $this->get('name') => false
        ];
    }

    /**
     * Action Ajax
     *
     * @return string
     */
    public function wp_ajax()
    {
        echo $this->displayItem(uniqid(), [], $this->appRequest('POST')->get('name'));

        exit;
    }
}