<?php

namespace tiFy\Components\AdminView\ListTable\RowAction;

class RowActionItemDuplicateController extends RowActionItemController
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