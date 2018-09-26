<?php

namespace tiFy\Layout\Share\ListTable;

use tiFy\Layout\Share\ListTable\Contracts\BulkActionCollectionInterface;
use tiFy\Layout\Share\ListTable\Contracts\ColumnCollectionInterface;
use tiFy\Layout\Share\ListTable\Contracts\ColumnItemInterface;
use tiFy\Layout\Share\ListTable\Contracts\ItemCollectionInterface;
use tiFy\Layout\Share\ListTable\Contracts\ItemInterface;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;
use tiFy\Layout\Share\ListTable\Contracts\PaginationInterface;
use tiFy\Layout\Display\ViewController;

class ListTableViewController extends ViewController
{
    /**
     * Instance de la disposition.
     * @var ListTableInterface
     */
    protected $layout;

    /**
     * Récupération du selecteur d'action groupées.
     *
     * @param string $which Choix de l'interface de navigation. top|bottom.
     *
     * @return BulkActionCollectionInterface
     */
    public function getBulkActions($which = '')
    {
        return $this->layout->getBulkActions($which);
    }

    /**
     * Récupération de la liste des colonnes.
     *
     * @return ColumnCollectionInterface|ColumnItemInterface[]
     */
    public function getColumns()
    {
        return $this->layout->columns();
    }

    /**
     * Récupération de la liste des entêtes HTML de colonnes.
     *
     * @param bool $with_id Activation de l'id HTML.
     *
     * @return string[]
     */
    public function getHeaderColumns($with_id = true)
    {
        return $this->layout->columns()->getHeaders($with_id);
    }

    /**
     * Récupération de la liste des éléments.
     *
     * @return ItemInterface[]
     */
    public function getItems()
    {
        return $this->layout->items()->all();
    }

    /**
     * Récupération d'une ligne de la table pour un élément.
     *
     * @param ItemInterface $item Liste des données de l'élément courant.
     *
     * @return void
     */
    public function getSingleRow($item)
    {
        return $this->layout->getSingleRow($item);
    }

    /**
     * Récupération de la liste des classes CSS de la balise table.
     *
     * @return string
     */
    public function getTableClasses()
    {
        return $this->layout->getTableClasses();
    }

    /**
     * Récupération de la liste des vues filtrées
     *
     * @return array
     */
    public function getViewFilters()
    {
        return $this->layout->getViewFilters();
    }

    /**
     * Vérification d'éxistance d'éléments.
     *
     * @return boolean
     */
    public function hasItems()
    {
        return $this->layout->items()->has();
    }

    /**
     * Récupération de la classe de rappel du controleur de pagination.
     *
     * @return PaginationInterface
     */
    public function pagination()
    {
        return $this->layout->pagination();
    }
}