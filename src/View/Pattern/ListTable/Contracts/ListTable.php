<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

use tiFy\Contracts\View\ViewPatternController;
use tiFy\View\Pattern\ListTable\Request\Request;

interface ListTable extends ViewPatternController
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
     * Récupération d'une instance d'élément à afficher dans une boucle d'itération.
     *
     * @return null|Item
     */
    public function item();

    /**
     * Récupération d'une instance de la liste des éléments à afficher.
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
     * Récupération de l'instance du controleur des actions sur un élément.
     *
     * @return string
     */
    public function rowActions();

    /**
     * Récupération de l'instance du controleur des filtres de la vue.
     *
     * @return ViewFiltersCollection
     */
    public function viewFilters();
}