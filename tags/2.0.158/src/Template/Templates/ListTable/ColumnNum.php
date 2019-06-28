<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable;

class ColumnNum extends Column
{
    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        return [
            'title' => __('#', 'tify')
        ];
    }

    /**
     * @inheritdoc
     */
    public function isPrimary(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function content(): string
    {
        return ($item = $this->factory->item())
            ? (string)($item->getIndex()+1)
            : '';
    }
}