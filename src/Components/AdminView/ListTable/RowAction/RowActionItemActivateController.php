<?php

namespace tiFy\Components\AdminView\ListTable\RowAction;

class RowActionItemActivateController extends RowActionItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Activer', 'tify'),
            'title'   => __('Activation de l\'élément', 'tify'),
            'nonce'   => $this->getNonce(),
            'attrs'   => ['style' => 'color:#006505;']
        ];
    }
}