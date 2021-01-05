<?php

declare(strict_types=1);

namespace tiFy\Metabox\Drivers;

use tiFy\Metabox\MetaboxDriver;

class ColorDriver extends MetaboxDriver implements ColorDriverInterface
{
    /**
     * @inheritDoc
     */
    protected $name = 'color';

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->title ?? __('Couleur', 'tify');
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->metaboxManager()->resources('/views/drivers/color');
    }
}
