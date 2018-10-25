<?php

namespace tiFy\Metabox;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Metabox\Metabox;
use tiFy\Metabox\MetaboxItemController;
use tiFy\Metabox\Tab\MetaboxTabDisplay;

class MetaboxServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $bindings = [
        MetaboxItemController::class,
        MetaboxTabDisplay::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(
            Metabox::class,
            function () {
                return new Metabox();
            }
        )->build();
    }
}