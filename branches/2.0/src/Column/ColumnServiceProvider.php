<?php

namespace tiFy\Column;

use tiFy\App\Container\AppServiceProvider;

class ColumnServiceProvider extends AppServiceProvider
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->app->singleton('column', function () { return new Column(); })->build();
        $this->app->bind('column.item', function ($name, $attrs = [], $screen = null) {
            return new ColumnItemController($name, $attrs, $screen);
        });
    }
}