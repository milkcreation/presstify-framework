<?php

namespace tiFy\Components\Layout\ListTable;

use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionInterface;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionInterface;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionInterface;
use tiFy\Components\Layout\ListTable\Item\ItemInterface;
use tiFy\Kernel\Layout\LayoutControllerInterface;

interface ListTableInterface extends LayoutControllerInterface
{
    /**
     * Récupération du controleur de gestion des colonnes.
     *
     * @return ColumnCollectionInterface
     */
    public function columns();

    /**
     * Récupération du selecteur d'action groupées.
     *
     * @param string $which Choix de l'interface de navigation. top|bottom.
     *
     * @return BulkActionCollectionInterface
     */
    public function getBulkActions($which = '');

    /**
     * Récupération de la liste des actions sur un élément.
     *
     * @param ItemInterface $item Liste des données de l'élément courant.
     * @param string $column_name Nom de qualification de la colonne courante.
     * @param string $primary Identifiant de qualification de la colonne principale
     *
     * @return string
     */
    public function getRowActions($item, $column_name, $primary);

    /**
     * Récupération d'une ligne de la table pour un élément.
     *
     * @param ItemInterface $item Liste des données de l'élément courant.
     *
     * @return void
     */
    public function getSingleRow($item);

    /**
     * Récupération de la liste des classes CSS de la balise table.
     *
     * @return array
     */
    public function getTableClasses();

    /**
     * Récupération de la liste des vues filtrées.
     *
     * @return array
     */
    public function getViewFilters();

    /**
     * Récupération de la liste des éléments.
     *
     * @return ItemCollectionInterface|ItemInterface[]
     */
    public function items();
}