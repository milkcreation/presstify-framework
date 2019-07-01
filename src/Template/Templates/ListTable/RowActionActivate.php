<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

class RowActionActivate extends RowAction
{
    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'content' => __('Activer', 'tify'),
            'title'   => __('Activation de l\'élément', 'tify'),
            'nonce'   => $this->getNonce(),
            'attrs'   => ['style' => 'color:#006505;']
        ];
    }
}