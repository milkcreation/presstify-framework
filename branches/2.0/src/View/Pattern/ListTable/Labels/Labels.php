<?php

namespace tiFy\View\Pattern\ListTable\Labels;

use tiFy\Kernel\Labels\LabelsBag;

class Labels extends LabelsBag
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