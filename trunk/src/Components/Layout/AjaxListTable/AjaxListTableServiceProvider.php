<?php

namespace tiFy\Components\Layout\AjaxListTable;

use tiFy\Apps\Layout\Db\DbInterface;
use tiFy\Apps\Layout\Labels\LabelsInterface;
use tiFy\Apps\Layout\Params\ParamsInterface;
use tiFy\Apps\Layout\Request\RequestInterface;
use tiFy\Components\Db\DbPostsController;
use tiFy\Components\Layout\AjaxListTable\Params\ParamsController;
use tiFy\Components\Layout\AjaxListTable\Request\RequestController;
use tiFy\Components\Layout\ListTable\ListTableServiceProvider;
use tiFy\Components\Layout\PostListTable\Column\ColumnItemPostTitleController;
use tiFy\PostType\PostTypeLabelsController;

class AjaxListTableServiceProvider extends ListTableServiceProvider
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var ListTableInterface
     */
    protected $app;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->app->singleton(DbInterface::class, function($app) {
            return new DbPostsController($app->getName(), [], $app);
        });

        $this->app->singleton(LabelsInterface::class, function($app) {
            return new PostTypeLabelsController($app->getName(), [], $app);
        });

        $this->app->singleton(ParamsInterface::class, function($app) {
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
                ColumnItemPostTitleController::class
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
                RequestInterface::class        => RequestController::class,
            ]
        );
    }
}