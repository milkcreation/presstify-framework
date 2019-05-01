<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Support\Collection;

interface ViewFiltersCollection extends Collection
{
    /**
     * Récupération de la liste des filtres.
     *
     * @return array|ViewFiltersItem[]
     */
    public function all();

    /**
     * Traitement de la liste des filtres.
     *
     * @param array $filters Liste des filtres.
     *
     * @return void
     */
    public function parse(array $filters = []): ViewFiltersCollection;
}