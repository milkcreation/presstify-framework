<?php

namespace tiFy\View\Pattern\ListTable\RowActions;

class RowActionsItemDuplicate extends RowActionsItem
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Dupliquer', 'tify'),
            'title'   => __('Duplication de l\'élément', 'tify'),
            'nonce'   => $this->getNonce()
        ];
    }
}