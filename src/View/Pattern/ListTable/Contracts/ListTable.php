<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

use tiFy\Contracts\View\PatternController;
use tiFy\View\Pattern\ListTable\Request\Request;

interface ListTable extends PatternController
{
    /**
     * Récupération de l'instance du controleur d'actions groupées.
     *
     * @return BulkActionsCollection
     */
    public function bulkActions();

    /**
     * Récupération de l'instance du controleur des colonnes.
     *
     * @return ColumnsCollection|ColumnsItem[]
     */
    public function columns();

    /**
     * Récupération de l'affichage d'une colonne.
     *
     * @param string $name Nom de qualification de la colonne.
     * @param Item $item Données de l'élément courant à afficher.
     *
     * @return string
     */
    public function getColumnDisplay($name, Item $item);

    /**
     * Récupération de la liste des classes CSS de la balise table.
     *
     * @return array
     */
    public function getTableClasses();

    /**
     * Récupération de la classe de rappel de récupération de la liste des éléments à afficher.
     *
     * @return Collection|Item[]
     */
    public function items();

    /**
     * Récupération de la classe de rappel de traitement de la pagination.
     *
     * @return Pagination
     */
    public function pagination();

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public function request();

    /**
     * Récupération d'une ligne de la table d'un élément.
     *
     * @param Item $item Liste des données de l'élément courant.
     *
     * @return string
     */
    public function row(Item $item);

    /**
     * Récupération de l'instance du controleur des actions sur un élément.
     *
     * @param Item $item Liste des données de l'élément courant.
     * @param string $column_name Nom de qualification de la colonne courante.
     * @param string $primary Identifiant de qualification de la colonne principale
     *
     * @return string
     */
    public function rowActions(Item $item, $column_name, $primary);

    /**
     * Récupération de l'instance du controleur des filtres de la vue.
     *
     * @return ViewFiltersCollection
     */
    public function viewFilters();
}