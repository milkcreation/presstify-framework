<?php

namespace tiFy\Components\TabMetaboxes\PostType\TextRemainingExcerpt;

use tiFy\Field\Field;
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
    public function load($wp_screen)
    {
        $this->appAddAction(
            'add_meta_boxes',
            function () {
                remove_meta_box('postexcerpt', $this->getPostType(), 'normal');
            }
        );

        $this->appAddAction('admin_enqueue_scripts');
    }

    /**
     * Mise en file des scripts de l'interface d'administration
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        /** @var Field $field */
        $field = $this->appServiceGet(Field::class);
        $field->enqueue('TextRemaining');
    }

    /**
     * {@inheritdoc}
     */
    public function display($post, $args = [])
    {
        return Field::TextRemaining([
            'name'  => 'excerpt',
            'value' => $post->post_excerpt,
            'max'   => $args['max'],
        ]);
    }
}