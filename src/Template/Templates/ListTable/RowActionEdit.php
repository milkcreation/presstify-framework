<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

class RowActionEdit extends RowAction
{
    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return array_merge(parent::defaults(), [
            'attrs'   => [
                'title' => __('Modification de l\'élément', 'tify'),
            ],
            'content' => __('Modifier', 'tify'),
            'url'     => $this->factory->param('edit_base_uri'),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        return !is_null($this->url);
    }
}