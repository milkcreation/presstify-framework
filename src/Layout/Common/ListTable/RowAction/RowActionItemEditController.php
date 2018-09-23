<?php

namespace tiFy\Components\Layout\ListTable\RowAction;

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
            'href'    => $this->app->param('edit_base_uri'),
            'nonce'   => false,
            'referer' => false
        ];
    }
}