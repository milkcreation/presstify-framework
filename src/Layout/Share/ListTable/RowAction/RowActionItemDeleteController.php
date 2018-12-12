<?php

namespace tiFy\Layout\Share\ListTable\RowAction;

class RowActionItemDeleteController extends RowActionItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Supprimer définitivement', 'tify'),
            'title'   => __('Suppression définitive de l\'élément', 'tify'),
            'nonce'   => $this->getNonce(),
            'attrs'   => ['style' => 'color:#a00;']
        ];
    }
}