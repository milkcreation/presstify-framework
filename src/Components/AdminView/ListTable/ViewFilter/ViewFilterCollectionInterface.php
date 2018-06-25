<?php

namespace tiFy\Components\AdminView\ListTable\ViewFilter;

interface ViewFilterCollectionInterface
{
    /**
     * Récupération de la liste des filtres.
     *
     * @return array
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