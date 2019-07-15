<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

class RowActionDuplicate extends RowAction
{
    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'content' => __('Dupliquer', 'tify'),
            'title'   => __('Duplication de l\'élément', 'tify'),
            'nonce'   => $this->getNonce()
        ];
    }
}