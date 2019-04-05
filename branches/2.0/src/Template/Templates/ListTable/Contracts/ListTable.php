<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Contracts;

use tiFy\Contracts\Template\FactoryRequest;
use tiFy\Contracts\Template\TemplateFactory;

interface ListTable extends TemplateFactory
{
    /**
     * Récupération de l'instance du controleur de table Ajax
     */
    public function ajax(): ? Ajax;

    /**
     * Récupération de l'instance du controleur d'actions groupées.
     *
     * @return BulkActionsCollection
     */
    public function bulkActions(): BulkActionsCollection;

    /**
     * Récupération de l'instance du controleur des colonnes.
     *
     * @return ColumnsCollection|ColumnsItem[]
     */
    public function columns(): ColumnsCollection;

    /**
     * Récupération d'une instance d'élément à afficher dans une boucle d'itération.
     *
     * @return Item|null
     */
    public function item(): ?Item;

    /**
     * Récupération d'une instance de la liste des éléments à afficher.
     *
     * @return Collection|Item[]
     */
    public function items(): Collection;

    /**
     * Récupération de la classe de rappel de traitement de la pagination.
     *
     * @return Pagination
     */
    public function pagination(): Pagination;

    /**
     * {@inheritdoc}
     *
     * @return Request
     */
    public function request(): FactoryRequest;

    /**
     * Récupération de l'instance du controleur des actions sur un élément.
     *
     * @return RowActionsCollection
     */
    public function rowActions(): RowActionsCollection;

    /**
     * Récupération de l'instance du controleur des filtres de la vue.
     *
     * @return ViewFiltersCollection
     */
    public function viewFilters(): ViewFiltersCollection;
}