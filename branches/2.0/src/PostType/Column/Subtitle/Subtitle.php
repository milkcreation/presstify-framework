<?php

namespace tiFy\PostType\Column\Subtitle;

use tiFy\Column\AbstractColumnPostTypeDisplayController;

class Subtitle extends AbstractColumnPostTypeDisplayController
{
    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->Title ? : __('Sous-titre', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function content($column_name, $post_id, $var3 = null)
    {
        if ($subtitle = get_post_meta($post_id, '_subtitle', true)) :
            return $subtitle;
        else :
            return "<em style=\"color:#AAA;\">" . __('Aucun', 'tify') . "</em>";
        endif;
    }
}