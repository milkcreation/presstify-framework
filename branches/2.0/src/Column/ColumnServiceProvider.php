<?php

namespace tiFy\Column;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Column\Column;
use tiFy\Column\ColumnItemController;

class ColumnServiceProvider extends AppServiceProvider
{
    /**
     * Liste des services à instance multiples auto-déclarés.
     * @var string[]
     */
    protected $bindings = [
        ColumnItemController::class
    ];

    /**
     * Liste des services à instance unique auto-déclarés.
     * @var string[]
     */
    protected $singletons = [

    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(
            Column::class,
            function () {
                return new Column();
        })->build();
    }
}