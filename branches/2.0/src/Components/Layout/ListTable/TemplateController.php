<?php

namespace tiFy\Components\Layout\ListTable;

use tiFy\Apps\Templates\TemplateBaseController;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionInterface;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionInterface;
use tiFy\Components\Layout\ListTable\Column\ColumnInterface;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionInterface;
use tiFy\Components\Layout\ListTable\Item\ItemInterface;
use tiFy\Components\Layout\ListTable\ListTableInterface;
use tiFy\Kernel\Layout\Param\ParamCollectionInterface;
use tiFy\Kernel\Layout\LayoutControllerInterface;

class TemplateController extends TemplateBaseController
{
    /**
     * Classe de rappel de l'application
     * @var ListTableInterface
     */
    protected $app;

    /**
     * Récupération du selecteur d'action groupées.
     *
     * @param string $which Choix de l'interface de navigation. top|bottom.
     *
     * @return BulkActionCollectionInterface
     */
    public function getBulkActions($which = '')
    {
        return $this->app->getBulkActions($which);
    }

    /**
     * Récupération de la liste des colonnes.
     *
     * @return ColumnCollectionInterface|ColumnInterface[]
     */
    public function getColumns()
    {
        return $this->app->columns();
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
        return $this->app->columns()->getHeaders($with_id);
    }

    /**
     * Récupération d'un intitulé.
     *
     * @param string $key Clé d'index de l'intitulé.
     * @param string $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function getLabel($key, $default = '')
    {
        return $this->app->getLabel($key, $default);
    }

    /**
     * Récupération de la liste des éléments.
     *
     * @return ItemCollectionInterface|ItemInterface[]
     */
    public function getItems()
    {
        return $this->app->items();
    }

    /**
     * Récupération du nom de qualification de la vue.
     *
     * @return string
     */
    public function getName()
    {
        return $this->app->getName();
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
        return $this->app->getSingleRow($item);
    }

    /**
     * Récupération de la liste des classes CSS de la balise table.
     *
     * @return string
     */
    public function getTableClasses()
    {
        return $this->app->getTableClasses();
    }

    /**
     * Récupération de la liste des vues filtrées
     *
     * @return array
     */
    public function getViewFilters()
    {
        return $this->app->getViewFilters();
    }

    /**
     * Vérification d'éxistance d'éléments.
     *
     * @return bool
     */
    public function hasItems()
    {
        return $this->app->items()->has();
    }

    /**
     * Récupération de la classe de rappel de gestion des paramètres.
     *
     * @return ParamCollectionInterface
     */
    public function params()
    {
        return $this->app->params();
    }
}