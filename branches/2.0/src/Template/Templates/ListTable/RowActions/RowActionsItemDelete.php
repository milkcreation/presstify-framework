<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\RowActions;

class RowActionsItemDelete extends RowActionsItem
{
    /**
     * @inheritdoc
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