<?php

namespace tiFy\Field;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Field\Manager;

class FieldServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('field', function () { return new Manager(); })->build();
    }
}