<?php

namespace tiFy\Layout;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Layout\Layout;
use tiFy\Layout\LayoutContextAdmin;
use tiFy\Layout\LayoutContextFront;
use tiFy\Layout\LayoutFactoryAdmin;
use tiFy\Layout\LayoutFactoryFront;
use tiFy\Layout\LayoutMenuAdmin;

class LayoutServiceProvider extends AppServiceProvider
{
    /**
     * Liste des services à instance multiples auto-déclarés.
     * @var string[]
     */
    protected $bindings = [
        LayoutFactoryAdmin::class,
        LayoutFactoryFront::class,
        LayoutMenuAdmin::class
    ];

    /**
     * Liste des services à instance unique auto-déclarés.
     * @var string[]
     */
    protected $singletons = [
        Layout::class,
        LayoutContextAdmin::class,
        LayoutContextFront::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->resolve(Layout::class);
        $this->app->resolve(LayoutContextAdmin::class);
        $this->app->resolve(LayoutContextFront::class);
    }
}