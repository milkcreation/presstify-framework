<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Template\{FactoryAwareTrait, FactoryQueryBuilder};

interface QueryBuilder extends FactoryAwareTrait, FactoryQueryBuilder
{
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

    /**
     * Récupération du nombre total d'éléments sur la page courante.
     *
     * @return int
     */
    public function totalPage(): int;

    /**
     * Nombre total d'éléments.
     *
     * @return int
     */
    public function totalFounds(): int;

    /**
     * Nombre total de page.
     *
     * @return int
     */
    public function totalPaged(): int;
}