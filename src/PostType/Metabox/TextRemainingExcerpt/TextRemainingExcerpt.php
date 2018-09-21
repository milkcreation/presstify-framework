<?php

namespace tiFy\PostType\Metabox\TextRemainingExcerpt;

use tiFy\Metabox\AbstractMetaboxDisplayPostController;

class TextRemainingExcerpt extends AbstractMetaboxDisplayPostController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'max' => 255,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function display($post, $args = [])
    {
        return field(
            'text-remaining',
            [
                'name'  => 'excerpt',
                'value' => $post->post_excerpt,
                'max'   => $this->get('max')
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        add_action(
            'add_meta_boxes',
            function () {
                remove_meta_box('postexcerpt', $this->getPostType(), 'normal');
            }
        );

        add_action(
            'admin_enqueue_scripts',
            function () {
                field('text-remaining')->enqueue_scripts();
            }
        );
    }
}