<?php

namespace tiFy\Template\Templates\ListTable\RowActions;

class RowActionsItemUntrash extends RowActionsItem
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