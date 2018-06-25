<?php

namespace tiFy\Components\AdminView\PostListTable;

use tiFy\Components\AdminView\ListTable\ListTable;
use tiFy\Components\AdminView\PostListTable\Param\ParamCollectionController;
use tiFy\Components\AdminView\PostListTable\Column\ColumnItemPostTitleController;
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