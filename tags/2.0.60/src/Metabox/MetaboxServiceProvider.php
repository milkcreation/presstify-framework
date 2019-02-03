<?php

namespace tiFy\Metabox;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Metabox\Tab\MetaboxTabController;

class MetaboxServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('metabox', function () { return new MetaboxManager(); })->build();

        $this->app->bind(
            'metabox.factory',
            function ($name, $attrs = [], $screen = null) {
                return new MetaboxFactory($name, $attrs, $screen);
            }
        );

        $this->app->bind(
            'metabox.tab',
            function ($attrs = [], $screen = null) {
                return new MetaboxTabController($attrs, $screen);
            }
        );
    }
}