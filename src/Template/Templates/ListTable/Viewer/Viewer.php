<?php

namespace tiFy\Template\Templates\ListTable\Viewer;

use tiFy\Template\Templates\ListTable\ListTable;
use tiFy\Template\Templates\BaseViewer;

/**
 * Class ListTableViewController
 * @package tiFy\Template\Templates
 *
 */
class Viewer extends BaseViewer
{
    /**
     * Instance de la disposition.
     * @var ListTable
     */
    protected $template;

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