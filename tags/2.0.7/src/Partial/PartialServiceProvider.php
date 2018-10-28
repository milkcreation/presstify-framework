<?php

namespace tiFy\Partial;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Partial\Manager;

class PartialServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('partial', function() { return new Manager(); })->build();
    }
}