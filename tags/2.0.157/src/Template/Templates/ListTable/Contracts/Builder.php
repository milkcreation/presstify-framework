<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Template\{FactoryAwareTrait, FactoryBuilder};

interface Builder extends FactoryAwareTrait, FactoryBuilder
{
    /**
     * Définition de la liste des éléments.
     *
     * @return
     */
    public function setItems(): Builder;

    /**
     * Vérifie si la recherche est active.
     *
     * @return boolean
     */
    public function searchExists(): bool;

    /**
     * Récupération des mots clefs de recherche.
     *
     * @return string
     */
    public function searchTerm(): string;
}