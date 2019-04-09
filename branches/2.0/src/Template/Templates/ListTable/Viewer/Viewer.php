<?php declare(strict_types=1);

namespace tiFy\Template\Templates\ListTable\Viewer;

use tiFy\Contracts\Template\{FactoryLabels, FactoryParams};
use tiFy\Template\Factory\FactoryViewer;
use tiFy\Template\Templates\ListTable\Contracts\{BulkActionsCollection,
    Collection,
    ColumnsCollection,
    Pagination,
    Request,
    Search,
    ViewFiltersCollection};
use tiFy\Template\Templates\ListTable\ListTable;


/**
 * Class Viewer
 * @package tiFy\Template\Templates\ListTable\Viewer
 *
 * @method BulkActionsCollection bulkActions()
 * @method ColumnsCollection columns()
 * @method Collection items()
 * @method FactoryLabels|string label(?string $key = null, string $default = '')
 * @method string name()
 * @method Pagination pagination()
 * @method FactoryParams|mixed param($key = null, $default = null)
 * @method Request request()
 * @method Search search()
 * @method ViewFiltersCollection viewFilters()
 */
class Viewer extends FactoryViewer
{
    /**
     * Instance du gabarit associé.
     * @var ListTable
     */
    protected $factory;

    /**
     * @inheritdoc
     */
    public function boot()
    {
        parent::boot();

        array_push(
            $this->mixins,
            'bulkActions',
            'columns',
            'items',
            'pagination',
            'request',
            'row',
            'search',
            'viewFilters'
        );
    }
}