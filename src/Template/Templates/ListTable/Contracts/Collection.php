<?php

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Kernel\QueryCollection;

interface Collection extends QueryCollection
{
    /**
     * Traitement de récupération de la liste des éléments.
     *
     * @param array $query_args Liste des arguments de requête de récupération
     *
     * @return void
     */
    public function query($query_args = []);
}