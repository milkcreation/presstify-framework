<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Contracts\Template\{FactoryAwareTrait, FactoryBuilder};

interface Builder extends FactoryAwareTrait, FactoryBuilder
{
    /**
     * Récupération des mots clefs de recherche.
     *
     * @return string
     */
    public function getSearch(): string;

    /**
     * Aggrégation des conditions de recherche de la requête de récupération des éléments.
     *
     * @return EloquentBuilder
     */
    public function querySearch(): EloquentBuilder;

    /**
     * Définition de la liste des éléments.
     *
     * @return Builder
     */
    public function setItems(): Builder;

    /**
     * Définition des mots clefs de recherche.
     *
     * @param string $search
     *
     * @return string
     */
    public function setSearch(string $search): Builder;
}