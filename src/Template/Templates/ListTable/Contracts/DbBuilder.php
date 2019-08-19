<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Template\{FactoryAwareTrait, FactoryDbBuilder};

interface DbBuilder extends FactoryAwareTrait, FactoryDbBuilder
{
    /**
     * Récupération de la liste des éléments.
     *
     * @return static
     */
    public function fetchItems(): DbBuilder;
}