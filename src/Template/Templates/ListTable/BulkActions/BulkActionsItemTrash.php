<?php

namespace tiFy\Template\Templates\ListTable\BulkActions;

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