<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

class RowActionEdit extends RowAction
{
    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return !empty($this->get('href', ''));
    }
}