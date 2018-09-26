<?php

namespace tiFy\Layout\Share\ListTable\RowAction;

class RowActionItemDeactivateController extends RowActionItemController
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