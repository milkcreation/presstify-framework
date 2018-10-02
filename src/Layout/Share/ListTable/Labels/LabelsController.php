<?php

namespace tiFy\Layout\Share\ListTable\Labels;

use tiFy\Layout\Base\LabelsBaseController;

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