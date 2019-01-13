<?php

namespace tiFy\View\Pattern\ListTable\RowActions;

class RowActionsItemDeactivate extends RowActionsItem
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Désactiver', 'tify'),
            'title'   => __('Désactivation de l\'élément', 'tify'),
            'nonce'   => $this->getNonce(),
            'attrs'   => ['style' => 'color:#D98500;']
        ];
    }
}