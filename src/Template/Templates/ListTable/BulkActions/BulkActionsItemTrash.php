<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\BulkActions;

class BulkActionsItemTrash extends BulkActionsItem
{
    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return array_merge(parent::defaults(), [
            'content' => __('Mettre à la corbeille', 'tify')
        ]);
    }
}