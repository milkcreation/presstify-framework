<?php

namespace tiFy\View\Pattern\ListTable;

use tiFy\View\Pattern\ListTable\Contracts\ListTable;
use tiFy\View\Pattern\PatternViewController;

/**
 * Class ListTableViewController
 * @package tiFy\View\Pattern
 *
 * @mixin \tiFy\View\Pattern\ListTable\ListTable
 */
class ListTableViewController extends PatternViewController
{
    /**
     * Instance de la disposition.
     * @var ListTable
     */
    protected $pattern;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        array_push(
            $this->mixins,
            'bulkActions',
            'columns',
            'label',
            'items',
            'pagination',
            'request',
            'row',
            'viewFilters'
        );
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
        return $this->pattern->columns()->getHeaders($with_id);
    }

    /**
     * Récupération de la liste des classes CSS de la balise table.
     *
     * @return array
     */
    public function getTableClasses()
    {
        return $this->pattern->getTableClasses();
    }
}