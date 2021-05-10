<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Column\Drivers;

use tiFy\Wordpress\Column\AbstractColumnDisplayPostTypeController;

class PostExcerptColumn extends AbstractColumnDisplayPostTypeController
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
    public function content($column_name = null, $post_id = null, $null = null)
    {
        if ($post = get_post($post_id)) :
            return $post->post_excerpt;
        endif;
    }
}