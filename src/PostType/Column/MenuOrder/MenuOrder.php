<?php

namespace tiFy\PostType\Column\MenuOrder;

use tiFy\Column\AbstractColumnPostTypeDisplayController;

class MenuOrder extends AbstractColumnPostTypeDisplayController
{
    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->Title ?: __('Ordre d\'affich.', 'tify');
    }

    /**
     * {@inheritdoc}
     */
    public function content($column_name, $post_id, $var3 = null)
    {
        $level = 0;
        $post = get_post($post_id);

        if (0 == $level && (int)$post->post_parent > 0) :
            $find_main_page = (int)$post->post_parent;
            while ($find_main_page > 0) :
                $parent = get_post($find_main_page);

                if (is_null($parent)) :
                    break;
                endif;

                $level++;
                $find_main_page = (int)$parent->post_parent;
            endwhile;
        endif;
        $_level = "";

        for ($i = 0; $i < $level; $i++) :
            $_level .= "<strong>&mdash;</strong> ";
        endfor;

        return $_level . $post->menu_order;
    }
}