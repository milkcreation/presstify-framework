<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\PartialDriver;

/**
 * @todo
 */
class MenuDriver extends PartialDriver implements MenuDriverInterface
{
    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->partialManager()->resources("/views/menu");
    }
}