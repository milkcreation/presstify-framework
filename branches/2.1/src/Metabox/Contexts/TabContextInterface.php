<?php

declare(strict_types=1);

namespace tiFy\Metabox\Contexts;

use tiFy\Metabox\MetaboxContextInterface;

interface TabContextInterface extends MetaboxContextInterface
{
    /**
     * Définition de l'onglet actif.
     *
     * @param string $tab
     *
     * @return static
     */
    public function setActive(string $tab): TabContextInterface;
}
