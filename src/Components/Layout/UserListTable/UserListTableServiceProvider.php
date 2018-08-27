<?php

namespace tiFy\Components\Layout\UserListTable;

use tiFy\App\Layout\Db\DbInterface;
use tiFy\App\Layout\Labels\LabelsInterface;
use tiFy\App\Layout\Params\ParamsInterface;
use tiFy\App\Layout\Request\RequestInterface;
use tiFy\Components\Db\DbUsersController;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionInterface;
use tiFy\Components\Layout\ListTable\Item\ItemInterface;
use tiFy\Components\Layout\ListTable\ListTableServiceProvider;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemRoleController;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemUserLoginController;
use tiFy\Components\Layout\UserListTable\Column\ColumnItemUserRegisteredController;
use tiFy\Components\Layout\UserListTable\Item\ItemCollectionController;
use tiFy\Components\Layout\UserListTable\Item\ItemController;
use tiFy\Components\Layout\UserListTable\Labels\LabelsController;
use tiFy\Components\Layout\UserListTable\Params\ParamsController;
use tiFy\Components\Layout\UserListTable\Request\RequestController;

class UserListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->app->singleton(DbInterface::class, function ($app) {
            return new DbUsersController($app->getName(), [], $app);
        });

        $this->app->singleton(LabelsInterface::class, function ($app) {
            return new LabelsController($app->getName(), [], $app);
        });

        $this->app->singleton(ParamsInterface::class, function ($app) {
            return new ParamsController($app->get('params', []), $app);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getBindings()
    {
        return array_merge(
            parent::getBindings(),
            [
                ColumnItemRoleController::class,
                ColumnItemUserLoginController::class,
                ColumnItemUserRegisteredController::class,
                ItemInterface::class => ItemController::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSingletons()
    {
        return array_merge(
            parent::getSingletons(),
            [
                ItemCollectionInterface::class => ItemCollectionController::class,
                RequestInterface::class        => RequestController::class,
            ]
        );
    }
}