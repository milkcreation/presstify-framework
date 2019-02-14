<?php

namespace tiFy\Template\Templates\ListTable\RowActions;

class RowActionsItemActivate extends RowActionsItem
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