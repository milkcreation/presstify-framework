<?php

namespace tiFy\Partial;

use tiFy\App\Container\AppServiceProvider;

class PartialServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('partial', function() { return new PartialManager(); })->build();
    }
}