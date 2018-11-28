<?php

namespace tiFy\Layout\Share\ListTable\BulkAction;

use tiFy\Layout\Share\ListTable\BulkAction\BulkActionItemController;

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