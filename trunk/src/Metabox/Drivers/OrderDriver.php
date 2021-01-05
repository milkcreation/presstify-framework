<?php

declare(strict_types=1);

namespace tiFy\Metabox\Drivers;

use tiFy\Metabox\MetaboxDriver;

class OrderDriver extends MetaboxDriver implements OrderDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'order';

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(
            parent::defaultParams(),
            [
                'attrs' => [
                    'min' => -1,
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Ordre d\'affichage', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metaboxManager()->resources('/views/drivers/order');
    }
}
