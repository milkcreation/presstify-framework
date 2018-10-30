<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use tiFy\Layout\Share\ListTable\Contracts\ViewFilterItemInterface;

interface ViewFilterCollectionInterface
{
    /**
     * Récupération de la liste des filtres.
     *
     * @return void|ViewFilterItemInterface[]
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