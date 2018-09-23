<?php

namespace tiFy\Components\Layout\ListTable\BulkAction;

use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemController;

class BulkActionItemTrashController extends BulkActionItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Mettre à la corbeille', 'tify')
        ];
    }
}