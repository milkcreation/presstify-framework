<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Template\{FactoryAwareTrait, FactoryDbBuilder};

interface DbBuilder extends FactoryAwareTrait, FactoryDbBuilder
{
    /**
     * Suppression d'un élément.
     *
     * @param string $key Indice de qualification de l'élément.
     *
     * @return bool
     */
    public function deleteItem(string $key): bool;

    /**
     * Récupération de l'instance d'un élément.
     *
     * @param string $key Indice de qualification de l'élément.
     *
     * @return Item|null
     */
    public function getItem(string $key): ?Item;

    /**
     * Retrouve la liste des éléments à afficher.
     *
     * @return static
     */
    public function fetchItems(): DbBuilder;
}