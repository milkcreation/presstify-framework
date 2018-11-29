<?php

namespace tiFy\Column;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Column\Column;
use tiFy\Column\ColumnItemController;

class ColumnServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('column', function () { return new Column(); })->build();
        $this->app->bind('column.item', function ($screen, $name, $attrs = []) { return new ColumnItemController($screen, $name, $attrs); });
    }
}