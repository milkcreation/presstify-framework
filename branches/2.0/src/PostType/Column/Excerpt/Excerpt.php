<?php

namespace tiFy\PostType\Column\Excerpt;

use tiFy\Column\AbstractColumnPostTypeDisplayController;

class Excerpt extends AbstractColumnPostTypeDisplayController
{
    /**
     * {@inheritdoc}
     */
    public function header()
    {
        return $this->item->getTitle() ? : __('Extrait', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function content($column_name, $post_id, $var3 = null)
    {
        if ($post = get_post($post_id)) :
            return $post->post_excerpt;
        endif;
    }
}