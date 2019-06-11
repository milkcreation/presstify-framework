<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

class RowActionDeactivate extends RowAction
{
    /**
     * @inheritDoc
     */
    public function defaults()
    {
        return [
            'content' => __('Désactiver', 'tify'),
            'title'   => __('Désactivation de l\'élément', 'tify'),
            'nonce'   => $this->getNonce(),
            'attrs'   => ['style' => 'color:#D98500;']
        ];
    }
}