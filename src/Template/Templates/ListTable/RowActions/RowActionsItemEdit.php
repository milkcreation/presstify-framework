<?php

namespace tiFy\Template\Templates\ListTable\RowActions;

class RowActionsItemEdit extends RowActionsItem
{
    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return [
            'content' => __('Modifier', 'tify'),
            'title'   => __('Modification de l\'élément', 'tify'),
            'href'    => $this->factory->param('edit_base_uri'),
            'nonce'   => false,
            'referer' => false
        ];
    }

    /**
     * @inheritdoc
     */
    public function isActive(): bool
    {
        return !empty($this->get('href', ''));
    }
}