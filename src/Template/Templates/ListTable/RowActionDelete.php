<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

class RowActionDelete extends RowAction
{
    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'content' => __('Supprimer définitivement', 'tify'),
            'title'   => __('Suppression définitive de l\'élément', 'tify'),
            'nonce'   => $this->getNonce(),
            'attrs'   => ['style' => 'color:#a00;']
        ];
    }
}