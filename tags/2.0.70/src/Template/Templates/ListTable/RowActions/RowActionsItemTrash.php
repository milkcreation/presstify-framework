<?php

namespace tiFy\Template\Templates\ListTable\RowActions;

class RowActionsItemTrash extends RowActionsItem
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