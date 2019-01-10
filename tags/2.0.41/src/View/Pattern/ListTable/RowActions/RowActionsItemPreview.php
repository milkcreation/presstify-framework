<?php

namespace tiFy\View\Pattern\ListTable\RowActions;

class RowActionsItemPreview extends RowActionsItem
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