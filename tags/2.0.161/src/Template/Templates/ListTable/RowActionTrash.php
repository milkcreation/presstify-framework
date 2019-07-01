<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

class RowActionTrash extends RowAction
{
    /**
     * @inheritDoc
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