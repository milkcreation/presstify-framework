<?php

namespace tiFy\Components\Layout\PostListTable;

use tiFy\Apps\Layout\Db\DbInterface;
use tiFy\Apps\Layout\Labels\LabelsInterface;
use tiFy\Apps\Layout\Params\ParamsInterface;
use tiFy\Apps\Layout\Request\RequestInterface;
use tiFy\Components\Db\DbPostsController;
use tiFy\Components\Layout\PostListTable\Column\ColumnItemPostTitleController;
use tiFy\Components\Layout\ListTable\ListTableServiceProvider;
use tiFy\Components\Layout\PostListTable\Params\ParamsController;
use tiFy\Components\Layout\PostListTable\Request\RequestController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemAllController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemPublishController;
use tiFy\Components\Layout\PostListTable\ViewFilter\ViewFilterItemTrashController;
use tiFy\PostType\PostTypeLabelsController;

class PostListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->app->singleton(DbInterface::class, function ($app) {
            return new DbPostsController($app->getName(), [], $app);
        });

        $this->app->singleton(LabelsInterface::class, function ($app) {
            return new PostTypeLabelsController($app->getName(), [], $app);
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
                ColumnItemPostTitleController::class,
                ViewFilterItemAllController::class,
                ViewFilterItemPublishController::class,
                ViewFilterItemTrashController::class,
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
                RequestInterface::class => RequestController::class,
            ]
        );
    }
}