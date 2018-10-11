<?php

namespace tiFy\Layout;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Layout\Layout;
use tiFy\Layout\LayoutAdmin;
use tiFy\Layout\LayoutAdminFactory;
use tiFy\Layout\LayoutAdminMenu;
use tiFy\Layout\LayoutFront;
use tiFy\Layout\LayoutFrontFactory;

class LayoutServiceProvider extends AppServiceProvider
{
    /**
     * Liste des services à instance multiples auto-déclarés.
     * @var string[]
     */
    protected $bindings = [
        LayoutMenuAdmin::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('layout', function () {return new Layout();})->build();

        $this->app->singleton('layout.admin', function () {return new LayoutAdmin();})->build();
        $this->app->bind('layout.admin.factory', LayoutAdminFactory::class);
        $this->app->bind('layout.admin.menu', LayoutAdminMenu::class);

        $this->app->singleton('layout.front', function () {return new LayoutFront();})->build();
        $this->app->bind('layout.front.factory', LayoutFrontFactory::class);
    }
}