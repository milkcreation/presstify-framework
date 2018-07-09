<?php

namespace tiFy\Components\Layout\UserListTable\Labels;

use tiFy\Apps\Layout\Labels\LabelsBaseController;

class LabelsController extends LabelsBaseController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'all_items'    => __('Utilisateurs', 'tify'),
            'search_items' => __('Rechercher un utilisateur', 'tify')
        ];
    }
}