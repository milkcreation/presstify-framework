<?php

namespace tiFy\Components\Layout\UserListTable;

use tiFy\Components\Db\DbUsersController;
use tiFy\Components\Layout\ListTable\ListTableServiceProvider;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemRoleController;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemUserLoginController;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemUserRegisteredController;
use tiFy\Components\Layout\UserListTable\Item\ItemCollectionController;
use tiFy\Components\Layout\UserListTable\Item\ItemController;
use tiFy\Components\Layout\UserListTable\Labels\LabelsController;
use tiFy\Components\Layout\UserListTable\Param\ParamCollectionController;
use tiFy\Components\Layout\UserListTable\Request\RequestController;

class UserListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return array_merge(
            parent::defaults(),
            [
                'columns.item.role'            => ColumnItemRoleController::class,
                'columns.item.user_login'      => ColumnItemUserLoginController::class,
                'columns.item.user_registered' => ColumnItemUserRegisteredController::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function parseConcrete($key, $default)
    {
        switch ($key) :
            default :
                return parent::parseConcrete($key, $default);
                break;
            case 'db' :
                return DbUsersController::class;
                break;
            case 'items' :
                return ItemCollectionController::class;
                break;
            case 'item' :
                return ItemController::class;
                break;
            case 'labels' :
                return LabelsController::class;
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