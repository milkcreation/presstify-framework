<?php

namespace tiFy\Components\Layout\ListTable\RowAction;

class RowActionItemUntrashController extends RowActionItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content'   => __('Rétablir', 'tify'),
            'title'     => __('Restauration de l\'élément', 'tify'),
            'nonce'     => $this->getNonce(),
            'referer' => true
        ];
    }
}