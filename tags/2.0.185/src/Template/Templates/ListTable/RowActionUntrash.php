<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

class RowActionUntrash extends RowAction
{
    /**
     * @inheritDoc
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