<?php

namespace tiFy\App\Layout;

use tiFy\App\Container\ServiceProvider;
use tiFy\App\Layout\Db\DbBaseController;
use tiFy\App\Layout\Db\DbInterface;
use tiFy\App\Layout\Labels\LabelsBaseController;
use tiFy\App\Layout\Labels\LabelsInterface;
use tiFy\App\Layout\LayoutInterface;
use tiFy\App\Layout\Notices\NoticesBaseController;
use tiFy\App\Layout\Notices\NoticesInterface;
use tiFy\App\Layout\Params\ParamsBaseController;
use tiFy\App\Layout\Params\ParamsInterface;
use tiFy\App\Layout\Request\RequestBaseController;
use tiFy\App\Layout\Request\RequestInterface;

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