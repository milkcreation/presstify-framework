<?php

namespace tiFy\View\Pattern\ListTable\BulkActions;

class BulkActionsItemTrash extends BulkActionsItem
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        $defaults = parent::defaults();
        $defaults['content'] = __('Mettre à la corbeille', 'tify');

        return $defaults;
    }
}