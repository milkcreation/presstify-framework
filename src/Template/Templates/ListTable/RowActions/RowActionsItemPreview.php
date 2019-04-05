<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\RowActions;

class RowActionsItemPreview extends RowActionsItem
{
    /**
     * @inheritdoc
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