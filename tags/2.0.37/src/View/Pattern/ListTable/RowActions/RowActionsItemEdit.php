<?php

namespace tiFy\View\Pattern\ListTable\RowActions;

class RowActionsItemEdit extends RowActionsItem
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Modifier', 'tify'),
            'title'   => __('Modification de l\'élément', 'tify'),
            'href'    => $this->pattern->param('edit_base_uri'),
            'nonce'   => false,
            'referer' => false
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return !empty($this->get('href', ''));
    }
}