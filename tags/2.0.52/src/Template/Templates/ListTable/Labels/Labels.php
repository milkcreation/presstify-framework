<?php

namespace tiFy\Template\Templates\ListTable\Labels;

use tiFy\Template\Templates\BaseLabels;

class Labels extends BaseLabels
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
                'no_items'     => __('Aucun élément trouvé.', 'tify'),
                'page_title'   => __('Tous les éléments', 'tify')
            ]
        );
    }
}