<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

class RowActionPreview extends RowAction
{
    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'content' => __('Prévisualisation', 'tify'),
            'title'   => __('Prévisualisation de l\'élément', 'tify'),
            'nonce'   => $this->getNonce(),
            'class'   => 'preview_item'
        ];
    }
}