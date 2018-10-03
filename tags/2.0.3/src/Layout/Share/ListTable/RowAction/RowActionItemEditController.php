<?php

namespace tiFy\Layout\Share\ListTable\RowAction;

class RowActionItemEditController extends RowActionItemController
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Modifier', 'tify'),
            'title'   => __('Modification de l\'élément', 'tify'),
            'href'    => $this->layout->param('edit_base_uri'),
            'nonce'   => false,
            'referer' => false
        ];
    }
}