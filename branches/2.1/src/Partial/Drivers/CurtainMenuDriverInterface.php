<?php

declare(strict_types=1);

namespace tiFy\Partial\Drivers;

use tiFy\Partial\PartialDriverInterface;

interface CurtainMenuDriverInterface extends PartialDriverInterface
{
    /**
     * Traitement de la liste des éléments.
     *
     * @return static
     */
    public function parseItems(): CurtainMenuDriverInterface;
}