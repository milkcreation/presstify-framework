<?php

namespace tiFy\Components\Layout\ListTable\Labels;

use tiFy\App\Layout\Labels\LabelsBaseController;

class LabelsController extends LabelsBaseController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'all_items'    => __('Tous les éléments', 'tify'),
            'search_items' => __('Rechercher un élément', 'tify')
        ];
    }
}