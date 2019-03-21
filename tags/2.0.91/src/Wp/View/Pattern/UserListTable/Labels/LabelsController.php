<?php

namespace tiFy\Layout\Share\WpUserListTable\Labels;

use tiFy\Layout\Share\ListTable\Labels\LabelsController as ShareListTableLabelsController;

class LabelsController extends ShareListTableLabelsController
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