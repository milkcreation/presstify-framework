<?php

namespace tiFy\Components\AdminView\ListTable\RowAction;

class RowActionItemPreviewController extends RowActionItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Prévisualisation', 'tify'),
            'title'   => __('Prévisualisation de l\'élément', 'tify'),
            'nonce'   => $this->getNonce(),
            'class'   => 'preview_item'
        ];
    }
}