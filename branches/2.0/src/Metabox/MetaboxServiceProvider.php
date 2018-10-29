<?php

namespace tiFy\Metabox;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Metabox\MetaboxManager;
use tiFy\Metabox\MetaboxFactory;
use tiFy\Metabox\Tab\MetaboxTabDisplay;

class MetaboxServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $bindings = [
        MetaboxTabDisplay::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('metabox', function () { return new MetaboxManager(); })->build();

        $this->app->bind(
            'metabox.factory',
            function ($name, $screen = null, $attrs = []) {
                return new MetaboxFactory($name, $screen, $attrs);
            }
        );
    }
}