<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

use tiFy\Contracts\Kernel\Collection;

interface ViewFiltersCollection extends Collection
{
    /**
     * Récupération de la liste des filtres.
     *
     * @return void|ViewFiltersItem[]
     */
    public function all();

    /**
     * Traitement de la liste des filtres.
     *
     * @param array $filters Liste des filtres.
     *
     * @return void
     */
    public function parse($filters = []);
}