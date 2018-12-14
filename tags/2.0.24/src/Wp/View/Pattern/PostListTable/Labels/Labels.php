<?php

namespace tiFy\Wp\View\Pattern\PostListTable\Labels;

use tiFy\PostType\PostTypeLabels;

class Labels extends PostTypeLabels
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return array_merge(
            parent::defaults(),
            [
                'all_items'    => __('Tous les éléments', 'tify'),
                'search_items' => __('Rechercher un élément', 'tify'),
                'no_items'     => __('No items found.'),
                'page_title'   => __('Tous les éléments', 'tify')
            ]
        );
    }
}