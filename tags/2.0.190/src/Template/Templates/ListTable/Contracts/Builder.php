<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Template\{FactoryAwareTrait, FactoryBuilder};

interface Builder extends FactoryAwareTrait, FactoryBuilder
{
    /**
     * Récupération de la liste des éléments.
     *
     * @return static
     */
    public function fetchItems(): Builder;
}