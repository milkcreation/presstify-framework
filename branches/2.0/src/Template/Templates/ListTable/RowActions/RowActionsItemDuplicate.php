<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\RowActions;

class RowActionsItemDuplicate extends RowActionsItem
{
    /**
     * @inheritdoc
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