<?php

namespace tiFy\Components\AdminView\ListTable\Column;

use Illuminate\Support\Collection;
use tiFy\Components\AdminView\ListTable\Column\ColumnItemInterface;
use tiFy\AdminView\AdminViewInterface;

interface ColumnCollectionInterface
{
    /**
     * Récupération de la liste des colonnes.
     *
     * @return Collection|ColumnItemInterface[]
     */
    public function all();

    /**
     * Récupération d'une colonne.
     *
     * @param string $name Nom de qualification.
     *
     * @return ColumnItemInterface
     */
    public function get($name);

    /**
     * Récupération de la liste de définition des colonnes.
     * @internal Couple nom de qualification => intitulé.
     *
     * @return array
     */
    public function getList();

    /**
     * Récupération de la liste des noms de qualification des colonnes masquées.
     *
     * @return string[]
     */
    public function getHidden();

    /**
     * Récupération de la liste des informations complètes concernant les colonnes.
     *
     * @return array
     */
    public function getInfos();

    /**
     * Récupération de la liste des noms de qualification des colonnes ouverte à l'ordonnacement.
     *
     * @return string[]
     */
    public function getSortable();

    /**
     * Récupération de la liste des noms de qualification des colonnes visibles.
     *
     * @return string[]
     */
    public function getVisible();

    /**
     * Traitement de la liste des colonnes.
     *
     * @param array $columns Liste des colonnes.
     *
     * @return void
     */
    public function parse($columns = []);
}