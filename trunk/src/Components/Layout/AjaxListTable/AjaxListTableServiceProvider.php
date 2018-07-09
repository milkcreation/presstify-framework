<?php

namespace tiFy\Components\Layout\AjaxListTable;

use tiFy\Components\Db\DbPostsController;
use tiFy\Components\Layout\AjaxListTable\Param\ParamCollectionController;
use tiFy\Components\Layout\AjaxListTable\Request\RequestController;
use tiFy\Components\Layout\ListTable\ListTableServiceProvider;
use tiFy\Components\Layout\PostListTable\Column\ColumnItemPostTitleController;
use tiFy\PostType\PostTypeLabelsController;

class AjaxListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return array_merge(
            parent::defaults(),
            [
                'columns.item.post_title' => [
                    'alias'     => ColumnItemPostTitleController::class,
                    'concrete'  => ColumnItemPostTitleController::class
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parseConcrete($key, $default)
    {
        switch($key) :
            default :
                return parent::parseConcrete($key, $default);
                break;
            case 'db' :
                return DbPostsController::class;
                break;
            case 'labels' :
                return PostTypeLabelsController::class;
                break;
            case 'params' :
                return ParamCollectionController::class;
                break;
            case 'request' :
                return RequestController::class;
                break;
        endswitch;
    }
}