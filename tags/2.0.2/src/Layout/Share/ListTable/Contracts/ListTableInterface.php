<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Layout\Share\ListTable\Contracts\BulkActionCollectionInterface;
use tiFy\Layout\Share\ListTable\Contracts\ColumnCollectionInterface;
use tiFy\Layout\Share\ListTable\Contracts\ItemCollectionInterface;
use tiFy\Layout\Share\ListTable\Contracts\ItemInterface;
use tiFy\Layout\Share\ListTable\Contracts\PaginationInterface;
use tiFy\Layout\Share\ListTable\Contracts\RequestInterface;

interface ListTableInterface extends LayoutDisplayInterface
{
    /**
     * Récupération du controleur de gestion des colonnes.
     *
     * @return ColumnCollectionInterface
     */
    public function columns();

    /**
     * Récupération de l'affichage d'une colonne.
     *
     * @param string $name Nom de qualification de la colonne.
     * @param ItemInterface $item Données de l'élément courant à afficher.
     *
     * @return string
     */
    public function getColumnDisplay($name, $item);

    /**
     * Récupération des information complètes concernant les colonnes
     *
     * @return array
     */
    public function getColumnInfos();

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
     * Récupération de la classe de rappel de récupération de la liste des éléments à afficher.
     *
     * @return ItemCollectionInterface
     */
    public function items();

    /**
     * Récupération de la classe de rappel de traitement de la pagination.
     *
     * @return PaginationInterface
     */
    public function pagination();

    /**
     * {@inheritdoc}
     *
     * @return RequestInterface
     */
    public function request();
}