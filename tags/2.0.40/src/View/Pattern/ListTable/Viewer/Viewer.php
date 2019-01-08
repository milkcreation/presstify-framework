<?php

namespace tiFy\View\Pattern\ListTable\Viewer;

use tiFy\View\Pattern\ListTable\ListTable;
use tiFy\View\Pattern\PatternBaseViewer;

/**
 * Class ListTableViewController
 * @package tiFy\View\Pattern
 *
 * @mixin ListTable
 */
class Viewer extends PatternBaseViewer
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
}