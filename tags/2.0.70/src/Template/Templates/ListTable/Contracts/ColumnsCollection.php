<?php

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Kernel\Collection as CollectionContract;

interface ColumnsCollection extends CollectionContract
{
    /**
     * Récupération de la liste des colonnes.
     *
     * @return ColumnsItem[]
     */
    public function all();

    /**
     * Récupération d'une colonne.
     *
     * @param string $name Nom de qualification.
     *
     * @return ColumnsItem
     */
    public function get($name, $default = null);

    /**
     * Récupération de la liste des noms de qualification des colonnes masquées.
     *
     * @return string[]
     */
    public function getHidden();

    /**
     * Récupération du nom de qualification de la colonne principale.
     *
     * @return string
     */
    public function getPrimary();

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
     * Récupération du nombre de colonne affichée.
     *
     * @return int
     */
    public function countVisible();

    /**
     * Traitement de la liste des colonnes.
     *
     * @param array $columns Liste des colonnes.
     *
     * @return void
     */
    public function parse($columns = []);
}