<?php

namespace tiFy\Apps\Layout;

use tiFy\Apps\Container\ServiceProvider;
use tiFy\Apps\Layout\Db\DbBaseController;
use tiFy\Apps\Layout\Db\DbInterface;
use tiFy\Apps\Layout\Labels\LabelsBaseController;
use tiFy\Apps\Layout\Labels\LabelsInterface;
use tiFy\Apps\Layout\LayoutInterface;
use tiFy\Apps\Layout\Notices\NoticesBaseController;
use tiFy\Apps\Layout\Notices\NoticesInterface;
use tiFy\Apps\Layout\Params\ParamsBaseController;
use tiFy\Apps\Layout\Params\ParamsInterface;
use tiFy\Apps\Layout\Request\RequestBaseController;
use tiFy\Apps\Layout\Request\RequestInterface;

class LayoutServiceProvider extends ServiceProvider
{
    /**
     * Classe de rappel du controleur de l'interface d'affichage associée.
     * @var LayoutInterface
     */
    protected $app;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(DbInterface::class, function($app) {
            return new DbBaseController($app->getName(), [], $app);
        });

        $this->app->singleton(LabelsInterface::class, function($app) {
            return new LabelsBaseController($app->getName(), [], $app);
        });

        $this->app->singleton(ParamsInterface::class, function($app) {
            return new ParamsBaseController($app->get('params', []), $app);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getSingletons()
    {
        return array_merge(
            parent::getSingletons(),
            [
                NoticesInterface::class => NoticesBaseController::class,
                RequestInterface::class => RequestBaseController::class
            ]
        );
    }
}