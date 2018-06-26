<?php

namespace tiFy\Components\Layout\PostListTable;

use tiFy\Components\Layout\ListTable\ListTable;
use tiFy\Components\Layout\PostListTable\Param\ParamCollectionController;
use tiFy\Components\Layout\PostListTable\Column\ColumnItemPostTitleController;
use tiFy\Components\Labels\LabelsPostTypeController;

class PostListTable extends ListTable
{
    /**
     * Controleur du fournisseur de service.
     * @var string
     */
    protected $serviceProvider = PostListTableServiceProvider::class;

    /**
     * {@inheritdoc}
     */
    public function getConcrete($key, $default = null)
    {
        switch($key) :
            default :
                return parent::getConcrete($key, $default);
                break;
            case 'labels' :
                return LabelsPostTypeController::class;
                break;
            case 'params' :
                return ParamCollectionController::class;
                break;
        endswitch;
    }
}