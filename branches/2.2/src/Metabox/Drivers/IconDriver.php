<?php

declare(strict_types=1);

namespace tiFy\Metabox\Drivers;

use tiFy\Metabox\MetaboxDriver;

class IconDriver extends MetaboxDriver implements IconDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'icon';

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Icône représentative', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metaboxManager()->resources('/views/drivers/icon');
    }
}
