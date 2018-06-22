<?php

namespace tiFy\Components\AdminView\ListTable\RowAction;

class RowActionItemTrashController extends RowActionItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Corbeille', 'tify'),
            'title'   => __('Mettre l\'élément à la corbeille', 'tify'),
            'nonce'   => $this->getNonce()
        ];
    }
}