<?php

namespace tiFy\Components\TabMetaboxes\PostType\TextRemainingExcerpt;

use tiFy\TabMetabox\ContentPostTypeController;

class TextRemainingExcerpt extends ContentPostTypeController
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
        $this->appAddAction(
            'add_meta_boxes',
            function () {
                remove_meta_box('postexcerpt', $this->getPostType(), 'normal');
            }
        );

        $this->appAddAction(
            'admin_enqueue_scripts',
            function () {
                field('text-remaining')->enqueue_scripts();
            }
        );
    }
}